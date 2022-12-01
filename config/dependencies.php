<?php

use Atsmacode\PokerGame\Models\TableSeat;

return [
    'invokables' => [
        Atsmacode\PokerGame\GameData\GameData::class,
        Atsmacode\PokerGame\HandStep\Start::class,
        Atsmacode\PokerGame\HandStep\Showdown::class,
        Atsmacode\PokerGame\PlayerHandler\PlayerHandler::class,
        Atsmacode\PokerGame\Game\PotLimitHoldEm::class,
        Atsmacode\PokerGame\Game\PotLimitOmaha::class,
        Atsmacode\PokerGame\ConfigProvider::class,
    ],
    'factories' => [
        Atsmacode\PokerGame\GamePlay\GamePlay::class 
            => Atsmacode\PokerGame\GamePlay\GamePlayFactory::class,
        Atsmacode\PokerGame\GameState\GameState::class 
            => Atsmacode\PokerGame\GameState\GameStateFactory::class,
        Atsmacode\PokerGame\ActionHandler\ActionHandler::class
            => Atsmacode\PokerGame\ActionHandler\ActionHandlerFactory::class,
        Atsmacode\PokerGame\Models\Street::class
            => Atsmacode\PokerGame\Models\StreetFactory::class,
        Atsmacode\PokerGame\HandStep\NewStreet::class
            => Atsmacode\PokerGame\HandStep\NewStreetFactory::class,
        Atsmacode\PokerGame\HandStep\Start::class
            => Atsmacode\PokerGame\HandStep\StartFactory::class,
        Atsmacode\PokerGame\Models\TableSeat::class
            => Atsmacode\PokerGame\Models\TableSeatFactory::class,
        Doctrine\DBAL\Connection::class
            => Atsmacode\PokerGame\DbalFactory::class,
        PDO::class
            => Atsmacode\PokerGame\PdoFactory::class,
    ]
];
