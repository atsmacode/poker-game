<?php

return [
    'invokables' => [
        Atsmacode\PokerGame\GameData\GameData::class,
        Atsmacode\PokerGame\HandStep\Start::class,
        Atsmacode\PokerGame\HandStep\NewStreet::class,
        Atsmacode\PokerGame\HandStep\Showdown::class,
        Atsmacode\PokerGame\PlayerHandler\PlayerHandler::class,
        Atsmacode\PokerGame\Game\PotLimitHoldEm::class,
        Atsmacode\PokerGame\Game\PotLimitOmaha::class,
    ],
    'factories' => [
        Atsmacode\PokerGame\GamePlay\GamePlay::class 
            => Atsmacode\PokerGame\GamePlay\GamePlayFactory::class,
        Atsmacode\PokerGame\GameState\GameState::class 
            => Atsmacode\PokerGame\GameState\GameStateFactory::class,
        Atsmacode\PokerGame\ActionHandler\ActionHandler::class
            => Atsmacode\PokerGame\ActionHandler\ActionHandlerFactory::class,
    ]
];
