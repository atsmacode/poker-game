<?php declare(strict_types=1);

namespace App\Classes\ActionHandler;

use App\Classes\GameState\GameState;
use App\Models\Hand;

interface ActionHandlerInterface
{
    /**
     * @param $int|null $betAmount
     */
    public function handle(
        Hand $hand,
        int  $playerId,
        int  $tableSeatId,
        int  $handStreetId,
             $betAmount,
        int  $actionId,
        int  $active,
        int  $stack
    ): GameState;
}