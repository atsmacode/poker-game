<?php

namespace App\Classes;

use App\Models\Hand;
use App\Models\HandType;
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

    public function decideWinner(): array
    {
        /*
         * foreach handType, if there are more than 1 players with that hand type,
         * retain only the one with the highest kicker & active cards as appropriate
         * then compare the hand rankings of each remaining player hand.
         */
        $playerHands      = $this->playerHands;
        $playerHandsReset = null;

        foreach($this->handIdentifier->handTypes as $handType){
            $playerHandsOfType = $this->filter('playerHands', 'handType', 'id', $handType->id);

            if(count($playerHandsOfType) > 1){
                $playerHandsReset = $this->identifyHighestRankedHandAndKickerOfThisType($playerHands, $playerHandsOfType, $handType);
            }
        }

        if($this->considerRankings || $this->considerKickers){
            return array_values($playerHandsReset)[0];
        }

        return $this->highestRankedPlayerHand();
    }

    protected function identifyHighestRankedHandAndKickerOfThisType(
        array $playerHands,
        array $playerHandsOfType,
        HandType $handType
    ): array
    {
        $this->considerRankings = true;

        /**
         * Remove hands of this type from the array. That way we can only 
         * retain the highest rank/kicker-ed hand and put it back in to be 
         * compared against the other highest ranked/kicker-ed hand types.
         */
        $playerHandsReset = array_filter($playerHands, function($value) use($handType){
            return $value['handType']->id !== $handType->id;
        });

        $handsOfThisTypeRanked = $this->getBestHandByHighestActiveCardRanksAndKickers(
            $playerHandsOfType
        );

        if(count($handsOfThisTypeRanked) > 1){

            $this->considerKickers = true;
            /*
             * TODO: split pots & kickers, this functionality is currently
             * set to only return the first one even if multiple players
             * share the same best active cards and kickers.
             */
        }

        $highestRankedHandOfType = $handsOfThisTypeRanked[array_key_first($handsOfThisTypeRanked)];

        array_push($playerHandsReset, $highestRankedHandOfType);

        return $playerHandsReset;
    }

    public function compileHands(): self
    {
        $this->getCommunityCards();

        foreach(TableSeat::getContinuingPlayerSeats($this->hand->id)->collect()->content as $tableSeat){
            $wholeCards = [];

            /**
             * TODO: Custom query, too many relations
             */
            foreach($tableSeat->player()->getWholeCards($this->hand->id) as $wholeCard){
                $wholeCards[] = $wholeCard;
            }

            $compileInfo = (new HandIdentifier())->identify($wholeCards, $this->communityCards)->identifiedHandType;
            $compileInfo['highestActiveCard'] = max($compileInfo['activeCards']);
            $compileInfo['player']            = $tableSeat->player();

            $this->playerHands[] = $compileInfo;
        }

        return $this;
    }

    public function getCommunityCards(): void
    {
        foreach($this->hand->streets()->collect()->content as $handStreet){
            foreach($handStreet->cards()->collect()->content as $handStreetCard){
                $this->communityCards[] = $handStreetCard->getCard();
            }
        }
    }

    /**
     * To filter an array of objects, specifying the $column where
     * the object resides in the array, and the $objProperty to 
     * filter by. $hayStack must be a property in this class.
     *
     * @param array $hayStack
     * @param $column
     * @param $objProperty
     * @param string|int $filter
     * @return array
     */
    private function filter(string $hayStack, string $column, string $objProperty, $filter): array
    {
        return array_filter($this->{$hayStack}, function($value) use($column, $objProperty, $filter){
            return $value[$column]->{$objProperty} == $filter;
        });
    }

    /**
     * @param array $hayStack
     */
    private function getBestHandByHighestActiveCardRanksAndKickers(array $playerHandsOfType): array
    {
        $maxKicker     = max(array_column($playerHandsOfType, 'kicker'));
        $maxActiveCard = max(array_column($playerHandsOfType, 'highestActiveCard'));

        /**
         * This can result in multiple winners (split pot).
         */
        return array_filter($playerHandsOfType, function($value) use($maxKicker, $maxActiveCard){
            return ($value['highestActiveCard'] == $maxActiveCard && $value['kicker'] == $maxKicker)
                || ($value['highestActiveCard'] == $maxActiveCard);
        });
    }

    /**
     * @return array
     */
    private function highestRankedPlayerHand(): array
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
