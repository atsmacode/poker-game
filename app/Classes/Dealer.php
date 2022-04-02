<?php

namespace App\Classes;

class Dealer
{

    public $deck;
    public $card;

    public function setDeck()
    {

        $this->deck = new Deck();

        return $this;
    }

    public function getDeck()
    {
        return $this->deck;
    }

    public function shuffle()
    {
        shuffle($this->deck->cards);

        return $this;
    }

    public function pickCard(string $rank = null, string $suit = null)
    {

        if($rank === null && $suit === null){
            $this->card = array_shift($this->deck->cards);
            return $this;
        }

        $filter = array_filter($this->deck->cards, function($value) use($rank, $suit){
            return $value->rank === $rank && $value->suit === $suit;
        });
        $this->card = array_values($filter);

        $card = $this->card;
        $reject = array_filter($this->deck->cards, function($value) use($card){
            return $value !== $card;
        });
        $this->deck = array_values($reject);

        return $this;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function dealTo($players, $cardCount, $hand = null)
    {

        foreach($players->collect()->content as $player){

            $dealtCards = 0;
            while($dealtCards < $cardCount){
                $player->wholeCards(true)->create([
                    'player_id' => $player->id,
                    'card_id' => $this->pickCard()->getCard()->id,
                    'hand_id' => $hand ? $hand->id : null
                ]);
                $dealtCards++;
            }

        }

        return $this;

    }

    public function dealStreetCards($handStreet, $cardCount)
    {

        $dealtCards = 0;

        while($dealtCards < $cardCount){

            $cardId = is_object($this->pickCard()->getCard()) ? $this->pickCard()->getCard()->id : $this->pickCard()->getCard()['id'];

            HandStreetCard::create([
                'card_id' => $cardId,
                'hand_street_id' => $handStreet->id
            ]);

            $dealtCards++;

        }

        return $this;

    }

    /*

    public function dealCardTo($player)
    {

        $player->wholeCards()->create([
            'card_id' => $this->getCard()->id
        ]);

        return $this;

    }

    public function dealStreetCards($handStreet, $cardCount)
    {

        $dealtCards = 0;

        while($dealtCards < $cardCount){

            $cardId = is_object($this->pickCard()->getCard()) ? $this->pickCard()->getCard()->id : $this->pickCard()->getCard()['id'];

            HandStreetCard::create([
                'card_id' => $cardId,
                'hand_street_id' => $handStreet->id
            ]);

            $dealtCards++;

        }

        return $this;

    }

    public function dealThisStreetCard($rank, $suit, $handStreet)
    {

        $cardId = $this->pickCard($rank, $suit)->getCard()->id;

        HandStreetCard::create([
            'card_id' => $cardId,
            'hand_street_id' => $handStreet->id
        ]);

        return $this;

    }*/
}
