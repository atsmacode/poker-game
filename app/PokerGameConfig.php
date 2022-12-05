<?php

namespace Atsmacode\PokerGame;

class PokerGameConfig
{
    const CONFIG_REF = 'config/poker_game.php';

    public function __invoke()
    {
        $config = require($GLOBALS['THE_ROOT'] . PokerGameConfig::CONFIG_REF);

        return $config['poker_game'];
    }
}
