<?php

namespace Atsmacode\PokerGame;

class PokerGameConfigProvider
{
    public function get(string $rootDir = null): array
    {
        $dbConfig         = require($rootDir . 'config/poker_game.php');
        $dependencyConfig = (new DependencyConfig())->get();

        return array_merge($dbConfig['poker_game'], $dependencyConfig); 
    }
}
