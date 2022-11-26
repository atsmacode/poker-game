<?php

namespace App\Classes\HandStep;

use App\Classes\GameState\GameState;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\TableSeat;

/**
 * Responsible for the actions required if a hand is to continue to the next street.
 */
class NewStreet extends HandStep
{
    public function handle(GameState $gameState, TableSeat $currentDealer = null): GameState
    {
        $this->gameState = $gameState;

        $this->updatePlayerStatusesOnNewStreet();

        $street = HandStreet::create([
            'street_id' => Street::find(['name' => $this->gameState->getGame()->streets[$this->gameState->handStreetCount()]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $street,
            $this->gameState->getGame()->streets[$this->gameState->incrementedHandStreets() - 1]['community_cards']
        );

        $this->gameState->updateHandStreets();

        return $this->gameState;
    }

    private function updatePlayerStatusesOnNewStreet()
    {
        TableSeat::find(['table_id' => $this->gameState->tableId()])
            ->updateBatch([
                'can_continue' => 0
            ], 'table_id = ' . $this->gameState->tableId());

        PlayerAction::find(['hand_id' => $this->gameState->handId()])
            ->updateBatch([
                'action_id' => null
            ], 'hand_id = ' . $this->gameState->handId());

        $this->gameState->setNewStreet();
    }
}