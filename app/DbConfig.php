<?php

namespace Atsmacode\PokerGame;

class DbConfig
{
    public function __invoke()
    {
        $dbTest = require($GLOBALS['THE_ROOT'] . 'config/db-test.php');
        $db     = require($GLOBALS['THE_ROOT'] . 'config/db.php');

        $dbConfig = [
            'db' => [
                'test'     => $dbTest,
                'live'     => $db,
                'provider' => $db['provider'],
            ],
        ];

        return $dbConfig;
    }
}
