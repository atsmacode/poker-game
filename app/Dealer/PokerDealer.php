<?php

namespace Atsmacode\PokerGame\Dealer;

use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\WholeCard;
use Atsmacode\CardGames\Dealer\Dealer;
use Atsmacode\PokerGame\Models\Deck;

class PokerDealer extends Dealer
{
    public function __construct(
        private WholeCard      $wholeCardModel,
        private HandStreetCard $handStreetCardModel,
        private Deck           $deckModel
    ) {}

    public function dealTo(array $players, int $cardCount, ?int $handId)
    {
        $dealtCards = 0;

        while($dealtCards < $cardCount){
            foreach($players as $player){
                $this->wholeCardModel->create([
                    'player_id' => $player['player_id'],
                    'card_id'   => $this->pickCard()->getCard()['id'],
                    'hand_id'   => $handId ?? null
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

            $this->handStreetCardModel->create([
                'card_id'        => $cardId,
                'hand_street_id' => $handStreet->getId()
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

        $this->handStreetCardModel->create([
            'card_id'        => $cardId,
            'hand_street_id' => $handStreet->getId()
        ]);

        return $this;
    }

    public function saveDeck(int $handId): void
    {
        $this->deckModel->create([
            'hand_id' => $handId,
            'cards'   => json_encode($this->deck)
        ]);
    }
}
