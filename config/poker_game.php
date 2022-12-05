<?php

return [
    'poker_game' => [
        'db' => [
            'live' => [
                'servername' => 'localhost',
                'username'   => 'root',
                'password'   => 'PASSWORD',
                'database'   => 'poker_game',
                'driver'     => 'pdo_mysql',
                'provider'   => Doctrine\DBAL\Connection::class
            ],
            'test' => [
                'servername' => 'localhost',
                'username'   => 'root',
                'password'   => 'PASSWORD',
                'database'   => 'poker_game_test',
                'driver'     => 'pdo_mysql',
                'provider'   => Doctrine\DBAL\Connection::class
            ],
        ],
    ],
];
