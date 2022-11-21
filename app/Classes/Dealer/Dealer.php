<?php

namespace App\Classes\Dealer;

use App\Classes\Deck\Deck;
use App\Models\HandStreetCard;
use App\Models\WholeCard;

class Dealer
{

    public $deck;
    public $card;

    public function setDeck($deck = null)
    {
        if ($deck) {
            $this->deck = $deck;
        } else {
            $this->deck = (new Deck())->cards;
        }

        return $this;
    }

    public function getDeck()
    {
        return $this->deck;
    }

    public function shuffle()
    {
        shuffle($this->deck);

        return $this;
    }

    public function pickCard(string $rank = null, string $suit = null)
    {
        if($rank === null && $suit === null){
            $card = array_shift($this->deck);

            $this->card = $card;

            $reject = array_filter($this->deck, function($value) use($card){
                return $value !== $card;
            });
            $this->deck = array_values($reject);

            return $this;
        }

        $filter = array_filter($this->deck, function($value) use($rank, $suit){
            return $value['rank'] === $rank && $value['suit'] === $suit;
        });
        $this->card = array_values($filter)[0];

        $card = $this->card;
        $reject = array_filter($this->deck, function($value) use($card){
            return $value !== $card;
        });
        $this->deck = array_values($reject);

        return $this;
    }

    public function getCard()
    {
        return $this->card;
    }

    public function dealTo(array $players, int $cardCount, $hand = null)
    {
        $dealtCards = 0;
        while($dealtCards < $cardCount){
            foreach($players as $player){
                WholeCard::create([
                    'player_id' => $player['player_id'],
                    'card_id'   => $this->pickCard()->getCard()['id'],
                    'hand_id'   => $hand ? $hand->id : null
                ]);
            }
            $dealtCards++;
        }

        return $this;
    }

    public function dealStreetCards($handStreet, $cardCount)
    {
        $dealtCards = 0;

        while($dealtCards < $cardCount){
            $cardId = $this->pickCard()->getCard()['id'];

            HandStreetCard::create([
                'card_id' => $cardId,
                'hand_street_id' => $handStreet->id
            ]);

            $dealtCards++;
        }

        return $this;
    }

    /**
     * @param HandStreet $handStreet
     * @param string $rank
     * @param string $suit
     * @return $this
     */
    public function dealThisStreetCard($rank, $suit, $handStreet)
    {
        $cardId = $this->pickCard($rank, $suit)->getCard()['id'];

        HandStreetCard::create([
            'card_id' => $cardId,
            'hand_street_id' => $handStreet->id
        ]);

        return $this;
    }
}
