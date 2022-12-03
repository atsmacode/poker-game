<?php

namespace Atsmacode\PokerGame;

class PokerGameConfig
{
    public function __invoke()
    {
        $dbTest = require($GLOBALS['THE_ROOT'] . 'config/db_poker_game_test.php');
        $db     = require($GLOBALS['THE_ROOT'] . 'config/db_poker_game.php');

        $dbConfig = [
            'db' => [
                'test' => $dbTest,
                'live' => $db,
            ],
        ];

        return $dbConfig;
    }
}
