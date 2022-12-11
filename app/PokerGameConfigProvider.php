<?php

namespace Atsmacode\PokerGame;

class PokerGameConfigProvider
{
    public function get(string $rootDir = null): array
    {
        $dbConfig         = require($rootDir . 'config/poker_game.php');
        $dependencyConfig = require($rootDir . 'config/dependencies.php');

        return array_merge($dbConfig['poker_game'], $dependencyConfig); 
    }
}
