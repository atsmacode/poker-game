<?php

namespace Atsmacode\PokerGame;

class PokerGameConfigProvider
{
    public function __construct(?string $rootDir = null)
    {
        $this->rootDir = $rootDir ?? '';
    }

    public function get(): array
    {
        $dbConfig         = require($this->rootDir . 'config/poker_game.php');
        $dependencyConfig = (new DependencyConfig())->get();

        return array_merge($dbConfig['poker_game'], $dependencyConfig); 
    }
}
