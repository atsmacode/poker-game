<?php

namespace Atsmacode\PokerGame\HandIdentifier;

use Atsmacode\PokerGame\Constants\HandType;
use Atsmacode\CardGames\Constants\Rank;
use Atsmacode\CardGames\Constants\Suit;

class HandIdentifier
{
    public $handTypes;

    public $identifiedHandType = [
        'handType'    => null,
        'activeCards' => [],
        'kicker'      => null
    ];

    public $allCards;
    public $highCard;
    public $pairs          = [];
    public $threeOfAKind   = false;
    public $straight       = false;
    public $flush          = false;
    public $fullHouse      = false;
    public $fourOfAKind    = false;
    public $straightFlush  = false;
    public $royalFlush     = false;
    
    protected $handMethods = [
        'hasRoyalFlush',
        'hasStraightFlush',
        'hasFourOfAKind',
        'hasFullHouse',
        'hasFlush',
        'hasStraight',
        'hasThreeOfAKind',
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
            if ($this->{$handMethod}() === true) { break; }
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
        if ($rank['ranking'] === 1) { return 14; }

        return false;
    }

    protected function thereIsNoAceInTheActiveCardsUnlessHandIsFlush($forHandCheck, $activeCards)
    {
        /** @todo Replace 1 & 14 with HIGH_ACE_ID & LOW_ACE_ID constants.*/
        return (
            $activeCards && count($this->filterAllCards('ranking', 1)) > 1
            && !in_array(1, $activeCards)
            && !in_array(14, $activeCards)
        ) || (in_array(1, $activeCards) && $forHandCheck === 'hasFlush');
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
        $cardRankings = array_column($this->sortAllCardsByDescRanking(), 'ranking');

        /**
         * Check against $this->highCard & activeCards so only
         * inactive cards are used as kickers kickers.
         * 
         * @todo This won't yet cover all cases as it will return
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
        return array_filter($this->allCards, function ($value) use ($column, $filter) {
            return $value[$column] === $filter;
        });
    }

    private function sortAllCardsByDescRanking(): array
    {
        usort($this->allCards, function ($a, $b) {
            if ($a['ranking'] == $b['ranking']) { return 0; }

            return $a['ranking'] > $b['ranking'] ? -1 : 1;
        });

        return array_values($this->allCards);
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
                if (count($this->allCards) > 2) {
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

    public function hasThreeOfAKind(): bool|self
    {
        foreach(Rank::ALL as $rank){
            if (3 === count($this->filterAllCards('rank_id', $rank['rank_id']))) {
                $this->threeOfAKind                        = $rank;
                $this->identifiedHandType['handType']      = $this->getHandType('Three of a Kind');
                $this->identifiedHandType['activeCards'][] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
                $this->identifiedHandType['kicker']        = $this->checkForAceKicker(__FUNCTION__, $this->identifiedHandType['activeCards'])
                    ?: $this->getKicker($this->identifiedHandType['activeCards']);

                return true;
            }
        }

        /** @todo There could be 2 trips - add handling for this */
        return $this;
    }

    public function hasStraight(): bool|self
    {
        if (true === $this->hasFiveHighStraight()) { return true; }

        if (true === $this->hasAceHighStraight()) { return true; }

        if (true === $this->hasAnyOtherStraight()) { return true; }

        return $this;
    }

    private function hasFiveHighStraight(): bool
    {
        $sortedCardsDesc = array_filter($this->sortAllCardsByDescRanking(), function ($value, $key) {
            $previousCardRanking = null;

            /* Remove duplicates. */
            if (array_key_exists($key - 1, $this->allCards)) {
                $previousCardRanking = $this->allCards[$key - 1]['ranking'];
            }

            switch ($value['ranking']) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    if ($value['ranking'] !== $previousCardRanking) { return true; }
                    break;
            }
        }, ARRAY_FILTER_USE_BOTH);

        $straight = array_slice($sortedCardsDesc, 0, 5);

        if ($straight && 5 === count($straight)) {
            $this->straight                       = $straight;
            $this->identifiedHandType['handType'] = $this->getHandType('Straight');
            $this->identifiedHandType['kicker']   = array_shift($straight)['ranking'];

            return true;
        }

        return false;
    }

    private function hasAceHighStraight(): bool
    {
        $sortedCardsDesc = array_filter($this->sortAllCardsByDescRanking(), function ($value, $key) {
            $previousCardRanking = null;

            /* Remove duplicates. */
            if (array_key_exists($key - 1, $this->allCards)) {
                $previousCardRanking = $this->allCards[$key - 1]['ranking'];
            }

            switch ($value['ranking']) {
                case 1:
                case 13:
                case 12:
                case 11:
                case 10:
                    if ($value['ranking'] !== $previousCardRanking) { return true; }
                    break;
            }
        }, ARRAY_FILTER_USE_BOTH);

        $straight = array_slice($sortedCardsDesc, 0, 5);

        if ($straight && 5 === count($straight)) {
            $this->straight                       = $straight;
            $this->identifiedHandType['handType'] = $this->getHandType('Straight');
            $this->identifiedHandType['kicker']   = 14;

            return true;
        }

        return false;
    }

    private function hasAnyOtherStraight(): bool
    {
        $cardsSortByDesc  = $this->sortAllCardsByDescRanking();
        $removeDuplicates = array_values(array_filter($cardsSortByDesc, function ($value, $key) use ($cardsSortByDesc) {
            if (array_key_exists($key - 1, $cardsSortByDesc)) {
                return $value['ranking'] !== $cardsSortByDesc[$key - 1]['ranking'];
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH));

        $straight = array_filter($removeDuplicates, function($value, $key) use ($removeDuplicates) {
            $nextCardRankingPlusOne      = null;
            $previousCardRankingMinusOne = null;
            $previousCardRanking         = null;

            if (array_key_exists($key + 1, $removeDuplicates)) {
                $nextCardRankingPlusOne = $removeDuplicates[$key + 1]['ranking'] + 1;
            }

            if (array_key_exists($key - 1, $removeDuplicates)) {
                $previousCardRankingMinusOne = $removeDuplicates[$key - 1]['ranking'] - 1;
                $previousCardRanking         = $removeDuplicates[$key - 1]['ranking'];
            }

            /** Had to add extra logic to prevent K,Q,9,8,7 being set as a straight, for example. */
            $twoCardsInFrontRankingPlusTwo   = null;
            $twoCardsPreviousRankingMinusTwo = null;

            if (array_key_exists($key + 2, $removeDuplicates)) {
                $twoCardsInFrontRankingPlusTwo = $removeDuplicates[$key + 2]['ranking'] + 2;
            }

            if (array_key_exists($key - 2, $removeDuplicates)) {
                $twoCardsPreviousRankingMinusTwo = $removeDuplicates[$key - 2]['ranking'] - 2;
            }

            return ($value['ranking'] !== $previousCardRanking) &&
                (($value['ranking'] === $previousCardRankingMinusOne || $value['ranking'] === $nextCardRankingPlusOne) &&
                    ($value['ranking'] === $twoCardsPreviousRankingMinusTwo || $value['ranking'] === $twoCardsInFrontRankingPlusTwo));
        }, ARRAY_FILTER_USE_BOTH);

        if ($straight && 5 === count($straight)) {
            $this->straight                       = $straight;
            $this->identifiedHandType['handType'] = $this->getHandType('Straight');
            $this->identifiedHandType['kicker']   = array_shift($straight)['ranking'];

            return true;
        }

        return false;
    }

    public function hasFlush(): bool|self
    {
        foreach (Suit::ALL as $suit) {
            $flushCards = $this->filterAllCards('suit_id', $suit['suit_id']);

            if (5 <= count($flushCards)) {
                $this->flush                               = $suit;
                $this->identifiedHandType['activeCards']   = array_column($flushCards, 'ranking');
                $this->identifiedHandType['handType']      = $this->getHandType('Flush');
                $this->identifiedHandType['kicker']        = $this->checkForAceKicker(__FUNCTION__, $this->identifiedHandType['activeCards'])
                    ?: $this->getKicker($this->identifiedHandType['activeCards']);

                return true;
            }
        }

        return $this;
    }

    public function hasFullHouse(): bool|self
    {
        $this->checkTripsForFullHouse()->checkPairsForFullHouse();

        /*
         * There could be 2 pairs here.
         * Changed to === 1 as three_of_a_kind_beats_two_pair_test_was_failing.
         * Needs looked into.
         */
        if ($this->threeOfAKind && 1 === count($this->pairs)) {
            $this->fullHouse                           = true;
            $this->identifiedHandType['handType']      = $this->getHandType('Full House');
            $this->identifiedHandType['activeCards']   = array_merge(
                $this->identifiedHandType['activeCards'],
                $this->pairs
            );

            return true;
        }

        $this->pairs                             = [];
        $this->threeOfAKind                      = false;
        $this->identifiedHandType['activeCards'] = [];

        return $this;
    }

    private function checkTripsForFullHouse(): self
    {
        foreach (Rank::ALL as $rank) {
            if (3 === count($this->filterAllCards('rank_id', $rank['rank_id']))) {
                $this->threeOfAKind                        = $rank;
                $this->identifiedHandType['activeCards'][] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
            }
        }

        return $this;
    }

    private function checkPairsForFullHouse(): self
    {
        foreach (Rank::ALL as $rank) {
            if (2 === count($this->filterAllCards('rank_id', $rank['rank_id'])) && $this->threeOfAKind !== $rank) {
                $this->pairs[] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
            }
        }

        return $this;
    }

    public function hasFourOfAKind(): bool|self
    {
        foreach (Rank::ALL as $rank) {
            if (4 === count($this->filterAllCards('rank_id', $rank['rank_id']))) {
                $this->fourOfAKind                         = $rank;
                $this->identifiedHandType['handType']      = $this->getHandType('Four of a Kind');
                $this->identifiedHandType['activeCards'][] = $this->checkForHighAceActiveCardRanking($rank) ?: $rank['ranking'];
                $this->identifiedHandType['kicker']        = $this->checkForAceKicker(__FUNCTION__, $this->identifiedHandType['activeCards'])
                    ?: $this->getKicker($this->identifiedHandType['activeCards']);

                return true;
            }
        }

        return $this;
    }

    public function hasStraightFlush(): bool|self
    {
        foreach(Suit::ALL as $suit){
            /* Remove cards not in this suit. This also takes care of duplicates. */
            $onlyThisSuit = array_values(array_filter($this->sortAllCardsByDescRanking(), function ($item) use ($suit) {
                return $item['suit_id'] === $suit['suit_id'];
            }));

            $straightFlush = array_filter($onlyThisSuit, function($value, $key) use ($suit, $onlyThisSuit) {
                $nextCardRankingPlusOne      = null;
                $previousCardRankingMinusOne = null;

                if (array_key_exists($key + 1, $onlyThisSuit)) {
                    $nextCardRankingPlusOne = $onlyThisSuit[$key + 1]['ranking'] + 1;
                }

                if (array_key_exists($key - 1, $onlyThisSuit)) {
                    $previousCardRankingMinusOne = $onlyThisSuit[$key - 1]['ranking'] - 1;
                }

                /*
                 * Had to add extra logic to prevent K,Q,9,8,7 being set as a straight, for example.
                 * And checking if the current rank has already been counted towards a straight.
                 * Which makes this method quite long - extract or simplify.
                 */
                $twoCardsInFrontRankingPlusTwo = null;
                $twoCardsPreviousRankingMinusTwo = null;

                if (array_key_exists($key + 2, $onlyThisSuit)) {
                    $twoCardsInFrontRankingPlusTwo = $onlyThisSuit[$key + 2]['ranking'] + 2;
                }

                if (array_key_exists($key - 2, $onlyThisSuit)) {
                    $twoCardsPreviousRankingMinusTwo = $onlyThisSuit[$key - 2]['ranking'] - 2;
                }

                return ($value['ranking'] === $previousCardRankingMinusOne || $value['ranking'] === $nextCardRankingPlusOne) &&
                    ($value['ranking'] === $twoCardsPreviousRankingMinusTwo || $value['ranking'] === $twoCardsInFrontRankingPlusTwo);
            }, ARRAY_FILTER_USE_BOTH);

            if ($straightFlush && 5 <= count($straightFlush)) {
                $this->straightFlush                  = true;
                $this->identifiedHandType['handType'] = $this->getHandType('Straight Flush');
                $this->identifiedHandType['kicker']   = $this->getMax($straightFlush, 'ranking');

                return true;
            }
        }

        return $this;
    }

    public function hasRoyalFlush(): bool|self
    {
        foreach (Suit::ALL as $suit) {
            $royalFlush = array_filter($this->allCards, function($value) use ($suit) {
                return $value['suit_id'] === $suit['suit_id'] && $value['rankAbbreviation'] === 'A' ||
                    $value['suit_id'] === $suit['suit_id'] && $value['rankAbbreviation'] === 'K' ||
                    $value['suit_id'] === $suit['suit_id'] && $value['rankAbbreviation'] === 'Q'||
                    $value['suit_id'] === $suit['suit_id'] && $value['rankAbbreviation'] === 'J'||
                    $value['suit_id'] === $suit['suit_id'] && $value['rankAbbreviation'] === '10';
            });

            if ($royalFlush && 5 === count($royalFlush)) {
                $this->royalFlush                        = $royalFlush;
                $this->identifiedHandType['activeCards'] = array_column($this->royalFlush, 'ranking');
                $this->identifiedHandType['handType']    = $this->getHandType('Royal Flush');

                return true;
            }
        }

        return $this;
    }
}
