<?php

namespace Atsmacode\PokerGame;

class ConfigDb
{
    const CONFIG_REF = '../config/poker_game.php';

    public function __invoke()
    {
        $config = require(ConfigDb::CONFIG_REF);

        return $config['poker_game'];
    }
}
