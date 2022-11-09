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
        foreach($this->handIdentifier->handTypes as $handType){
            $playerHandsOfType = $this->filter('playerHands', 'handType', 'id', $handType->id);

            if(count($playerHandsOfType) > 1){
                $this->identifyHighestRankedHandAndKickerOfThisType(
                    $this->playerHands,
                    $playerHandsOfType,
                    $handType
                );
            }
        }

        return $this->highestRankedPlayerHand();
    }

    protected function identifyHighestRankedHandAndKickerOfThisType(
        array $playerHands,
        array $playerHandsOfType,
        HandType $handType
    ): void {
        /**
         * Remove hands of this type from the array. That way we can only 
         * retain the highest rank/kicker-ed hand and put it back in to be 
         * compared against the other highest ranked/kicker-ed hand types.
         */
        $this->playerHands = array_filter($playerHands, function($value) use($handType){
            return $value['handType']->id !== $handType->id;
        });

        $handsOfThisTypeRanked = $this->getBestHandByHighestActiveCardRank(
            $playerHandsOfType
        );

        if(count($handsOfThisTypeRanked) > 1){
            /*
             * TODO: split pots, this functionality is currently
             * set to only return the first one even if multiple players
             * share the same best active cards and kickers.
             */
            $handsOfThisTypeRanked = $this->getBestHandByHighestKicker(
                $playerHandsOfType
            );
        }

        $highestRankedHandOfType = $handsOfThisTypeRanked[array_key_first($handsOfThisTypeRanked)];

        array_push($this->playerHands, $highestRankedHandOfType);
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
        /**
         * TODO: Custom query, too many relations
         */
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
    private function getBestHandByHighestActiveCardRank(array $playerHandsOfType): array
    {
        $maxActiveCard = max(array_column($playerHandsOfType, 'highestActiveCard'));

        return array_filter($playerHandsOfType, function($value) use($maxActiveCard){
            return $value['highestActiveCard'] == $maxActiveCard;
        });
    }

    /**
     * @param array $hayStack
     */
    private function getBestHandByHighestKicker(array $playerHandsOfType): array
    {
        $maxKicker = max(array_column($playerHandsOfType, 'kicker'));

        return array_filter($playerHandsOfType, function($value) use($maxKicker){
            return $value['kicker'] == $maxKicker;
        });
    }

    /**
     * @return array
     */
    private function highestRankedPlayerHand(): array
    {
        uasort($this->playerHands, function ($a, $b){
            /**
             * Why was this if statement added? TODO
             */
            if ($a['handType']->ranking == $b['handType']->ranking) {
                return 0;
            }
            return ($a['handType']->ranking > $b['handType']->ranking) ? 1 : -1;
        });

        return $this->playerHands[array_key_first($this->playerHands)];
    }
}
