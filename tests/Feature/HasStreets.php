<?php 

namespace Atsmacode\PokerGame\Tests\Feature;

use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\Street;

trait HasStreets
{
    protected function setFlop()
    {
        $flop = HandStreet::create([
            'street_id' => Street::find(['name' => 'Flop'])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $flop,
            $this->gameState->getGame()->streets[1]['community_cards']
        );
    }

    protected function setTurn()
    {
        $turn = HandStreet::create([
            'street_id' => Street::find(['name' => 'Turn'])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $turn,
            $this->gameState->getGame()->streets[2]['community_cards']
        );
    }

    protected function setRiver()
    {
        $river = HandStreet::create([
            'street_id' => Street::find(['name' => 'River'])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $river,
            $this->gameState->getGame()->streets[3]['community_cards']
        );
    }
}