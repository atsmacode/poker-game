<?php

namespace Atsmacode\PokerGame\SitHandler;

use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;

class SitHandler
{
    public function __construct(
        private GameState $gameState,
        private Table     $tableModel,
        private TableSeat $tableSeatModel
    ) {}

    public function sit(int $playerId): GameState
    {
        return $this->gameState;
    }
}