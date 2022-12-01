<?php

namespace Atsmacode\PokerGame\HandStep;

use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\PlayerAction;
use Atsmacode\PokerGame\Models\Street;
use Atsmacode\PokerGame\Models\TableSeat;

/**
 * Responsible for the actions required if a hand is to continue to the next street.
 */
class NewStreet extends HandStep
{
    private Street $streetModel;

    public function __construct(Street $streetModel)
    {
        $this->streetModel = $streetModel;
    }

    public function handle(GameState $gameState, TableSeat $currentDealer = null): GameState
    {
        $this->gameState = $gameState;

        $handStreet = HandStreet::create([
            'street_id' => $this->streetModel->find([
                'name' => $this->gameState->getGame()->streets[$this->gameState->handStreetCount()]['name']
            ])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->gameState->getGameDealer()->dealStreetCards(
            $handStreet,
            $this->gameState->getGame()->streets[$this->gameState->incrementedHandStreets() - 1]['community_cards']
        );

        $this->updatePlayerStatusesOnNewStreet($handStreet->id);
        $this->gameState->updateHandStreets();
        $this->gameState->setPlayers();

        return $this->gameState;
    }

    private function updatePlayerStatusesOnNewStreet(int $handStreetId): void
    {
        TableSeat::find(['table_id' => $this->gameState->tableId()])
            ->updateBatch([
                'can_continue' => 0
            ], 'table_id = ' . $this->gameState->tableId());

        PlayerAction::find(['hand_id' => $this->gameState->handId()])
            ->updateBatch([
                'action_id'      => null,
                'hand_street_id' => $handStreetId
            ], 'hand_id = ' . $this->gameState->handId());

        $this->gameState->setNewStreet();
    }
}