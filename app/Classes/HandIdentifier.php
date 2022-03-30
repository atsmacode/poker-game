<?php

namespace App\Classes;

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
        'hasThreeOfAKind',
        'hasTwoPair',
        'hasPair',*/
        'highestCard'
    ];

    public function __construct()
    {
        $this->handTypes = (new HandType())->all();
    }

    public function identify($wholeCards, $communityCards)
    {
        $this->allCards = array_merge($wholeCards, $communityCards);

        foreach($this->handMethods as $handMethod){
            if($this->{$handMethod}() === true){
                break;
            }
        }

        return $this;

    }

    /*protected function checkForAceKicker($allCards, $forHandCheck, $activeCards = null)
    {
        if($this->thereIsNoAceInTheActiveCardsUnlessHandIsFlush($allCards, $forHandCheck, $activeCards)){
            return 14;
        }

        return false;
    }*/

    protected function checkForHighAceActiveCardRanking($rank)
    {
        if($rank->ranking === 1){
            return 14;
        }

        return false;
    }

    /*protected function thereIsNoAceInTheActiveCardsUnlessHandIsFlush($allCards, $forHandCheck, $activeCards)
    {
        return ($activeCards && $allCards->contains('ranking', 1)
                && !in_array(1, $activeCards)
                && !in_array(14, $activeCards))
            || (in_array(1, $activeCards) && $forHandCheck === 'hasFlush');
    }*/

    private function getMax($haystack, $columm)
    {
        $rankings = array_column($haystack, $columm);

        return max($rankings);
    }

    private function getMin($haystack, $columm)
    {
        $rankings = array_column($haystack, $columm);

        return min($rankings);
    }

    private function getKicker()
    {
        $rankings = array_column($this->allCards, 'ranking');
        arsort($rankings);

        foreach($rankings as $ranking){
            if($ranking < $this->highCard){
                return $ranking;
            }
        }
    }

    private function getHandType($name)
    {
        foreach($this->handTypes as $handType){
            if($handType['name'] == $name){
                return $handType;
            }
        }
    }

    private function search($haystack, $column, $value)
    {
        foreach($this->{$haystack} as $item){
            if($item->{$column} == $value){
                return $item;
            }
        }
    }

    public function highestCard()
    {

        if($this->getMin($this->allCards, 'ranking') === 1){
            $this->highCard = 14;
        } else {
            $this->highCard = $this->getMax($this->allCards, 'ranking');
        }

        $this->identifiedHandType['handType'] = $this->getHandType('High Card');
        $this->identifiedHandType['activeCards'][] = $this->highCard;
        $this->identifiedHandType['kicker'] = $this->getKicker();

        return $this;
    }

}
