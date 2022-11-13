<?php

namespace App\Classes\HandIdentifier;

use App\Constants\HandType;
use App\Constants\Rank;

class HandIdentifier
{
    public $handTypes;
    public $identifiedHandType = [
        'handType' => null,
        'activeCards' => [0],
        'kicker' => null
    ];
    public $allCards;
    public $highCard;
    public $pairs = [];
    public $threeOfAKind = false;
    public $straight = false;
    public $flush = false;
    public $fullHouse = false;
    public $fourOfAKind = false;
    public $straightFlush = false;
    public $royalFlush = false;
    protected $handMethods = [
        /*'hasRoyalFlush',
        'hasStraightFlush',
        'hasFourOfAKind',
        'hasFullHouse',
        'hasFlush',
        'hasStraight',
        'hasThreeOfAKind',*/
        'hasTwoPair',
        'hasPair',
        'highestCard'
    ];

    public function __construct()
    {
        $this->handTypes = HandType::ALL;
    }

    public function identify(array $wholeCards, array $communityCards): self
    {
        $this->allCards = array_merge($wholeCards, $communityCards);

        foreach ($this->handMethods as $handMethod) {
            if ($this->{$handMethod}() === true) {
                break;
            }
        }

        return $this;
    }

    private function checkForAceKicker(string $forHandCheck, array $activeCards = null): int|bool
    {
        if ($this->thereIsNoAceInTheActiveCardsUnlessHandIsFlush($forHandCheck, $activeCards)) {
            return 14;
        }

        return false;
    }

    /**
     * Ace is technically ranked 1 in the DB, but because it can be
     * used high or low, we need to switch it to 14 so it can be
     * ranked higher than a king (13) if required.
     *
     * @param array<mixed> $rank
     * @return int|bool
     */
    private function checkForHighAceActiveCardRanking(array $rank): int|bool
    {
        if ($rank['ranking'] === 1) {
            return 14;
        }

        return false;
    }

    protected function thereIsNoAceInTheActiveCardsUnlessHandIsFlush($forHandCheck, $activeCards)
    {
        /**
         * TODO: Replace 1 & 14 with HIGH_ACE_ID
         * & LOW_ACE_ID constants.
         */
        return ($activeCards && count($this->filterAllCards('ranking', 1)) > 1
                && !in_array(1, $activeCards)
                && !in_array(14, $activeCards))
            || (in_array(1, $activeCards) && $forHandCheck === 'hasFlush');
    }

    /**
     * @param array|object $haystack
     * @param string $columm
     */
    private function getMax($haystack, string $columm)
    {
        return max(array_column($haystack, $columm));
    }

    /**
     * @param array|object $haystack
     * @param string $columm
     */
    private function getMin($haystack, $columm)
    {
        return min(array_column($haystack, $columm));
    }

    private function getKicker(array $activeCards = null): int
    {
        $cardRankings = array_column($this->sortCardsByDescRanking(), 'ranking');

        /**
         * Check against $this->highCard & activeCards so only
         * inactive cards are used as kickers kickers.
         * 
         * TODO: This won't yet cover all cases as it will return
         * null if none of the player's inactive cards meet the
         * two conditions.
         */
        foreach ($cardRankings as $cardRanking) {
            if (
                ($this->highCard && $cardRanking != $this->highCard) ||
                ($activeCards && !in_array($cardRanking, $activeCards))
            ) {
                return $cardRanking;
            }
        }
    }

    private function getHandType(string $name)
    {
        $key = array_search($name,
            array_column($this->handTypes, 'name')
        );

        if (array_key_exists($key, $this->handTypes)) {
            return $this->handTypes[$key];
        }

        return false;
    }

    private function filterAllCards(string $column, $filter)
    {
        return array_filter($this->allCards, function($value) use($column, $filter){
            /**
             * TODO: Remove temp is_array check, using Card objects in
             * tests and arrays in actual GamePlay/Showdown.
             */
            if( is_array($value)) {
                return $value[$column] === $filter;
            } else {
                return $value->{$column} === $filter;
            }
        });
    }

    /**
     * @return array<Card>
     */
    private function sortCardsByDescRanking()
    {
        uasort($this->allCards, function ($a, $b){
            /**
             * TODO: Remove temp is_array check, using Card objects in
             * tests and arrays in actual GamePlay/Showdown.
             */
            if( is_array($a)) { 
                if ($a['ranking'] == $b['ranking']) {
                    return 0;
                }
                return ($a['ranking'] > $b['ranking']) ? -1 : 1;
            } else {
                if ($a->ranking == $b->ranking) {
                    return 0;
                }
                return ($a->ranking > $b->ranking) ? -1 : 1;
            }
        });

        return $this->allCards;
    }

    public function highestCard(): self
    {
        if ($this->getMin($this->allCards, 'ranking') === 1) {
            $this->highCard = 14;
        } else {
            $this->highCard = $this->getMax($this->allCards, 'ranking');
        }

        $this->identifiedHandType['handType']      = $this->getHandType('High Card');
        $this->identifiedHandType['activeCards'][] = $this->highCard;
        $this->identifiedHandType['kicker']        = $this->getKicker();

        return $this;
    }

    public function hasPair(): bool|self
    {
        foreach (Rank::ALL as $rank) {
            if (count($this->filterAllCards('rank_id', $rank['rank_id'])) === 2) {
                $this->pairs[] = $rank;
                $this->identifiedHandType['activeCards'][] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
                /*
                 * The showdown may be called pre-flop when the pot is checked down to BB.
                 * In which case they may have a pair and no other kicker rank.
                 */
                if (count($this->allCards) > 2) {
                    $this->identifiedHandType['kicker'] = $this->checkForAceKicker(__FUNCTION__,  $this->identifiedHandType['activeCards'])
                        ?: $this->getKicker($this->identifiedHandType['activeCards']);
                } else {
                    $this->identifiedHandType['kicker'] = $rank['ranking'];
                }
            }
        }

        if (count($this->pairs) === 1) {
            $this->identifiedHandType['handType'] = $this->getHandType('Pair');
            return true;
        }

        return $this;
    }

    public function hasTwoPair(): bool|self
    {
        foreach(Rank::ALL as $rank){
            if (count($this->filterAllCards('rank_id', $rank['rank_id'])) === 2) {
                $this->pairs[]                             = $rank;
                $this->identifiedHandType['activeCards'][] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
                /*
                 * The showdown may be called pre-flop when the pot is checked down to BB.
                 * In which case they may have a pair and no other kicker rank.
                 */
                if(count($this->allCards) > 2){
                    $this->identifiedHandType['kicker'] = $this->checkForAceKicker(__FUNCTION__,  $this->identifiedHandType['activeCards'])
                        ?: $this->getKicker($this->identifiedHandType['activeCards']);
                } else {
                    $this->identifiedHandType['kicker'] = $rank['ranking'];
                }
            }
        }

        if (count($this->pairs) >= 2) {
            $this->identifiedHandType['handType'] = $this->getHandType('Two Pair');
            return true;
        }

        $this->pairs = [];

        return $this;
    }
}
