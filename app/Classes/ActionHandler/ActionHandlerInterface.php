<?php declare(strict_types=1);

namespace App\Classes\ActionHandler;

use App\Classes\GameState\GameState;
use App\Models\Hand;

interface ActionHandlerInterface
{
    public function handle(
        Hand $hand,
        int  $playerId,
        int  $tableSeatId,
        int  $handStreetId,
        int  $betAmount,
        int  $actionId,
        bool $active
    ): GameState;
}