<?php

namespace Atsmacode\PokerGame\Classes\Dealer;

use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\WholeCard;
use Atsmacode\CardGames\Classes\Dealer\Dealer;

class PokerDealer extends Dealer
{
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
