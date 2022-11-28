<?php

return [
    'invokables' => [
        Atsmacode\PokerGame\Classes\GameData\GameData::class,
        Atsmacode\PokerGame\Classes\HandStep\Start::class,
        Atsmacode\PokerGame\Classes\HandStep\NewStreet::class,
        Atsmacode\PokerGame\Classes\HandStep\Showdown::class,
        Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler::class,
        Atsmacode\PokerGame\Classes\Game\PotLimitHoldEm::class,
        Atsmacode\PokerGame\Classes\Game\PotLimitOmaha::class,
    ],
    'factories' => [
        Atsmacode\PokerGame\Classes\GamePlay\GamePlay::class => Atsmacode\PokerGame\Classes\GamePlay\GamePlayFactory::class,
    ]
];