<?php

namespace Atsmacode\PokerGame;

class DbConfig
{
    public function __invoke()
    {
        $dbTest = require('config/db-test.php');
        $db     = require('config/db.php');

        $dbConfig = [
            'db' => [
                'test' => $dbTest,
                'live' => $db,
            ],
        ];

        return $dbConfig;
    }
}
