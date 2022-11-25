<?php

namespace App\Classes\HandStep;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\Game;
use App\Classes\GameState\GameState;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\TableSeat;

class InProgress extends HandStep
{
    public function __construct(Game $game, Dealer $dealer)
    {
        $this->game   = $game;
        $this->dealer = $dealer;
    }

    public function handle(GameState $gameState): GameState
    {
        $this->gameState = $gameState;
        
        $this->updatePlayerStatusesOnNewStreet();

        $street = HandStreet::create([
            'street_id' => Street::find(['name' => $this->game->streets[$this->gameState->handStreetCount()]['name']])->id,
            'hand_id' => $this->gameState->handId()
        ]);

        $this->dealer->dealStreetCards(
            $street,
            $this->game->streets[$this->gameState->incrementedHandStreets() - 1]['community_cards']
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