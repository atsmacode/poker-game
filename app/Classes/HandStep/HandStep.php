<?php

namespace App\Classes\HandStep;

use App\Classes\GameState\GameState;

abstract class HandStep
{
    public function handle(GameState $gameState): GameState {}

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