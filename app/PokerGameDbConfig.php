<?php

namespace Atsmacode\PokerGame;

class PokerGameDbConfig
{
    const CONFIG_REF = 'config/poker_game.php';

    public function __invoke()
    {
        //$config = require(PokerGameConfig::CONFIG_REF);

        return [
            'db' => [
                'live' => [
                    'servername' => 'localhost',
                    'username'   => 'root',
                    'password'   => 'PASSWORD',
                    'database'   => 'poker_game',
                    'driver'     => 'pdo_mysql',
                ],
                'test' => [
                    'servername' => 'localhost',
                    'username'   => 'root',
                    'password'   => 'PASSWORD',
                    'database'   => 'poker_game_test',
                    'driver'     => 'pdo_mysql',
                ],
            ],
        ];
    }
}
