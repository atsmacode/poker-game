<?php

namespace App\Classes;

use App\Models\Hand;
use App\Models\TableSeat;

class Showdown
{
    public $handIdentifier;
    public $hand;
    public $winner;
    protected $considerKickers = false;
    protected $considerRankings = false;

    /**
     * @var array<Card>
     */
    public $communityCards = [];
    
    /**
     * @var array<mixed>
     */
    public array $playerHands = [];

    /**
     * @param Hand $hand
     */
    public function __construct($hand)
    {
        $this->handIdentifier = new HandIdentifier();
        $this->hand = $hand;
    }

    public function decideWinner()
    {
        /*
         * foreach handType
         * If there are more than 1 players with that hand type
         * Retain only the one with the highest kicker or active cards as appropriate
         * Then compare the hand rankings of each remaining player hand
         */
        $playerHands = $this->playerHands;
        $playerHandsReset = null;

        foreach($this->handIdentifier->handTypes as $handType){
            $playerHandsOfHandType = $this->filter('playerHands', 'handType', 'id', $handType->id);

            if(count($playerHandsOfHandType) > 1){
                $playerHandsReset = $this->identifyHighestRankedHandAndKickerOfThisType($playerHands, $playerHandsOfHandType, $handType);
            }
        }

        if($this->considerRankings || $this->considerKickers){
            return $playerHandsReset
                ->sortBy(function ($item) {
                    return $item['handType']->ranking;
                })
                ->values()
                ->first();
        }

        return $this->highestRankedPlayerHand($this->playerHands, 'handType', 'ranking');
    }

    protected function identifyHighestRankedHandAndKickerOfThisType($playerHands, $playerHandsOfHandType, $handType)
    {
        $this->considerRankings = true;

        // $playerHandsReset = $playerHands->reject(function($value) use($handType){
        //     return $value['handType']->id === $handType->id;
        // });

        $playerHandsReset = array_filter($playerHands, function($value) use($handType){
            return $value['handType']->id !== $handType->id;
        });

        // $highestRankedHandOfThisType = $playerHandsOfHandType->reject(function($value) use($playerHands, $handType){

        //     /*
        //      * Only reject less than, if multiple remain with same
        //      * highestActiveRanking we will consider kickers.
        //      */
        //     return max($value['activeCards']) < $playerHands
        //             ->where('handType' , $handType)
        //             ->sortByDesc('highestActiveCard')
        //             ->first()['highestActiveCard'];

        // });

        $highestRankedHandOfThisType = array_filter($playerHandsOfHandType, function($value) use($playerHands, $handType){
            return max($value['activeCards']) < $playerHands
                    ->where('handType' , $handType)
                    ->sortByDesc('highestActiveCard')
                    ->first()['highestActiveCard'];
        });

        if($highestRankedHandOfThisType->count() > 1){

            $this->considerKickers = true;

            $highestRankedHandOfThisType = $highestRankedHandOfThisType->reject(function($value) use($highestRankedHandOfThisType){

                /*
                 * Only reject less than, if multiple remain
                 * with same kicker it's a split pot.
                 */
                return $value['kicker'] < $highestRankedHandOfThisType
                        ->sortByDesc('kicker')
                        ->first()['kicker'];

            });
        }

        return $playerHandsReset->push($highestRankedHandOfThisType->first());
    }

    public function compileHands()
    {
        $this->getCommunityCards();

        foreach(TableSeat::find(['can_continue' => 1])->collect()->content as $tableSeat){
            $wholeCards = [];

            /**
             * TODO: Custom query, too many relations
             */
            foreach($tableSeat->player()->wholeCards()::find([
                'hand_id' => $this->hand->id, 'player_id' => $tableSeat->player()->id
            ])->collect()->content as $wholeCard){
                $wholeCards[] = $wholeCard->card();
            }

            $compileInfo = (new HandIdentifier())->identify($wholeCards, $this->communityCards)->identifiedHandType;
            $compileInfo['highestActiveCard'] = max($compileInfo['activeCards']);
            $compileInfo['player'] = $tableSeat->player();

            $this->playerHands[] = $compileInfo;
        }

        return $this;
    }

    public function getCommunityCards()
    {
        foreach($this->hand->streets()->collect()->content as $handStreet){
            foreach($handStreet->cards()->collect()->content as $handStreetCard){
                $this->communityCards[] = $handStreetCard->card();
            }
        }
    }

    /**
     * To filter an array of objects, specifying the column $column where
     * the object resides in the array, and the object property
     * $objProperty to filter by. $hayStack must be a property in
     * this class.
     *
     * @param array $hayStack
     * @param $column
     * @param $objProperty
     * @param string $filter
     * @return array
     */
    private function filter($hayStack, $column, $objProperty, $filter)
    {
        return array_filter($this->{$hayStack}, function($value) use($column, $objProperty, $filter){
            return $value[$column]->{$objProperty} === $filter;
        });
    }

    private function highestRankedPlayerHand()
    {
        uasort($this->playerHands, function ($a, $b){
            if ($a['handType']->ranking == $b['handType']->ranking) {
                return 0;
            }
            return ($a['handType']->ranking > $b['handType']->ranking) ? 1 : -1;
        });

        return $this->playerHands[array_key_first($this->playerHands)];
    }
}