# Environment

## PHP

8.1.3

## MySQL

8.0.13

## Vue.Js

^3.2.39

## Node.Js

18.12.1

# Commands

## Linux

Build the test DB:

>dev/builddb

Run the unit test suite:

>dev/phpunit

Individual Drop, Create and Seed commands. Remove '-d true' for prodution:

> php dev/PokerGameApp.php app:create-database -d true
> php dev/PokerGameApp.php app:build-card-games -d true
> php dev/PokerGameApp.php app:build-poker-game -d true

## Windows

Drop, Create and Seed all tables:

>.\dev\builddb.bat

Run the unit test suite:

>.\dev\runtests.bat

Individual Drop, Create and Seed commands. Remove '-d true' for prodution:

> php .\dev\PokerGameApp.php app:create-database -d true
> php .\dev\PokerGameApp.php app:build-card-games -d true
> php .\dev\PokerGameApp.php app:build-poker-game -d true

## Laragon

Using Laragon, the following example path to run PHP might be useful:

> C:\laragon\bin\php\php-8.1.3-nts-Win32-vs16-x64/php

# Configs

You need to add poker_game.php to configure your local DB credentials, like so:

```
<?php

return [
    'poker_game' => [
        'db' => [
            'live' => [
                'servername' => 'localhost',
                'username'   => 'DB_USER',
                'password'   => 'DB_PASSWORD',
                'database'   => 'poker_game',
                'driver'     => 'pdo_mysql',
            ],
            'test' => [
                'servername' => 'localhost',
                'username'   => 'DB_USER',
                'password'   => 'DB_PASSWORD',
                'database'   => 'poker_game_test',
                'driver'     => 'pdo_mysql',
            ],
        ],
    ],
];

```

# Todo:

- Review all TODO comments and implement solution
- Once everything above is tidied/finalised, add remaining unit tests from original app
- Add DB indexes, tests get slower once DB gets fuller
