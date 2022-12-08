<?php

namespace Atsmacode\PokerGame\Console\Commands;

use Atsmacode\Framework\Console\Commands\Migrator;
use Atsmacode\PokerGame\Database\Migrations\CreateActions;
use Atsmacode\PokerGame\Database\Migrations\CreateHands;
use Atsmacode\PokerGame\Database\Migrations\CreateHandTypes;
use Atsmacode\PokerGame\Database\Migrations\CreatePlayerActionLogs;
use Atsmacode\PokerGame\Database\Migrations\CreatePlayerActions;
use Atsmacode\PokerGame\Database\Migrations\CreatePlayers;
use Atsmacode\PokerGame\Database\Migrations\CreatePots;
use Atsmacode\PokerGame\Database\Migrations\CreateStacks;
use Atsmacode\PokerGame\Database\Migrations\CreateStreets;
use Atsmacode\PokerGame\Database\Migrations\CreateTables;
use Atsmacode\PokerGame\Database\Migrations\CreateWholeCards;
use Atsmacode\PokerGame\Database\Seeders\SeedActions;
use Atsmacode\PokerGame\Database\Seeders\SeedHandTypes;
use Atsmacode\PokerGame\Database\Seeders\SeedPlayers;
use Atsmacode\PokerGame\Database\Seeders\SeedStreets;
use Atsmacode\PokerGame\Database\Seeders\SeedTables;

#[AsCommand(
    name: 'app:build-poker-game',
    description: 'Populate the DB with all resources',
    hidden: false,
    aliases: ['app:build-poker-game']
)]

class BuildPokerGame extends Migrator
{
    protected array $buildClasses = [
        CreateHandTypes::class,
        CreatePlayers::class,
        CreateTables::class,
        CreateActions::class,
        CreateStreets::class,
        CreateHands::class,
        CreateWholeCards::class,
        CreatePlayerActions::class,
        CreateStacks::class,
        CreatePots::class,
        SeedHandTypes::class,
        SeedTables::class,
        SeedPlayers::class,
        SeedStreets::class,
        SeedActions::class,
        CreatePlayerActionLogs::class
    ];

    protected static $defaultName = 'app:build-poker-game';
}
