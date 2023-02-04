<?php 

namespace Atsmacode\PokerGame\Tests;

trait HasStreets
{
    protected function setFlop()
    {
        $flop = $this->handStreetModel->create([
            'street_id' =>  $this->streetModel->find(['name' => 'Flop'])->getId(),
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $flop,
            $this->gameState->getGame()->streets[1]['community_cards']
        );
    }

    protected function setTurn()
    {
        $turn = $this->handStreetModel->create([
            'street_id' =>  $this->streetModel->find(['name' => 'Turn'])->getId(),
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $turn,
            $this->gameState->getGame()->streets[2]['community_cards']
        );
    }

    protected function setRiver()
    {
        $river = $this->handStreetModel->create([
            'street_id' =>  $this->streetModel->find(['name' => 'River'])->getId(),
            'hand_id' => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $river,
            $this->gameState->getGame()->streets[3]['community_cards']
        );
    }
}