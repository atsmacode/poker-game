<?php declare(strict_types=1);

namespace Atsmacode\PokerGame\Classes\PlayerHandler;

use Atsmacode\PokerGame\Classes\GameState\GameState;

interface PlayerHandlerInterface
{
    public function handle(GameState $gameState): array;
}
