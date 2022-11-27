<?php

namespace Atsmacode\PokerGame\Classes\HandStep;

use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Models\TableSeat;

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