<?php

namespace App\Console\Commands;

use Database\Migrations\CreateActions;
use Database\Migrations\CreateCards;
use Database\Migrations\CreateDatabase;
use Database\Migrations\CreateHands;
use Database\Migrations\CreateHandTypes;
use Database\Migrations\CreatePlayerActions;
use Database\Migrations\CreatePlayers;
use Database\Migrations\CreateStreets;
use Database\Migrations\CreateTables;
use Database\Migrations\CreateWholeCards;
use Database\Seeders\SeedCards;
use Database\Seeders\SeedHandTypes;
use Database\Seeders\SeedPlayers;
use Database\Seeders\SeedStreets;
use Database\Seeders\SeedTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:build-env',
    description: 'Populate the DB with all resources',
    hidden: false,
    aliases: ['app:build-env']
)]

class BuildEnvironment extends Command
{

    private $buildClasses = [
        CreateDatabase::class,
        CreateCards::class,
        CreateHandTypes::class,
        CreatePlayers::class,
        CreateTables::class,
        CreateActions::class,
        CreateStreets::class,
        CreateHands::class,
        CreateWholeCards::class,
        CreatePlayerActions::class,
        SeedCards::class,
        SeedHandTypes::class,
        SeedTables::class,
        SeedPlayers::class,
        SeedStreets::class
    ];
    protected static $defaultName = 'app:build-env';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach($this->buildClasses as $class){
            foreach($class::$methods as $method){
                (new $class($output))->{$method}();
            }
        }

        return Command::SUCCESS;

    }


}