<?php

namespace App\Classes\HandStep;

use App\Classes\GameState\GameState;
use App\Models\TableSeat;

abstract class HandStep
{
    public function handle(GameState $gameState, TableSeat $currentDealer = null): GameState {}

    public function getGameState(): GameState
    {
        return $this->gameState;
    }

    public function setGameState(GameState $gameState): self
    {
        $this->gameState = $gameState;

        return $this;
    }
}