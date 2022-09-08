<?php

namespace App\Classes;

use App\Models\Hand;
use App\Models\TableSeat;

class Showdown
{
    public $handIdentifier;
    public $hand;
    public $communityCards = [];
    public $playerHands = [];
    public $winner;
    protected $considerKickers = false;
    protected $considerRankings = false;

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

        $playerHands = collect($this->playerHands);
        $playerHandsReset = null;

        foreach($this->handIdentifier->handTypes as $handType){

            $playerHandsOfHandType = $playerHands->where('handType.id' , $handType->id);

            if($playerHandsOfHandType->count() > 1){

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

        // Return the player hand with the highest rank
        return collect($this->playerHands)
            ->sortBy(function ($item) {
                return $item['handType']['ranking'];
            })
            ->values()
            ->first();
    }

    protected function identifyHighestRankedHandAndKickerOfThisType($playerHands, $playerHandsOfHandType, $handType)
    {
        $this->considerRankings = true;

        $playerHandsReset = $playerHands->reject(function($value) use($handType){
            return $value['handType']->id === $handType->id;
        });

        $highestRankedHandOfThisType = $playerHandsOfHandType->reject(function($value) use($playerHands, $handType){

            /*
             * Only reject less than, if multiple remain with same
             * highestActiveRanking we will consider kickers.
             */
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

        foreach(TableSeat::where('can_continue', 1)->get() as $tableSeat){
            $wholeCards = [];
            foreach($tableSeat->player->wholeCards->where('hand_id', $this->hand->id) as $wholeCard){
                $wholeCards[] = $wholeCard->card;
            }

            $compileInfo = (new HandIdentifier())->identify($wholeCards, $this->communityCards)->identifiedHandType;
            $compileInfo['highestActiveCard'] = max($compileInfo['activeCards']);
            $compileInfo['player'] = $tableSeat->player;

            $this->playerHands[] = $compileInfo;
        }

        return $this;

    }

    public function getCommunityCards()
    {
        foreach($this->hand->streets as $handStreets){
            foreach($handStreets->cards as $handStreetCard){
                $this->communityCards[] = $handStreetCard->card;
            }
        }
    }

}