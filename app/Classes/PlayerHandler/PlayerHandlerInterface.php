<?php declare(strict_types=1);

namespace App\Classes\PlayerHandler;

use App\Classes\GameState\GameState;

interface PlayerHandlerInterface
{
    public function handle(GameState $gameState): array;
}
