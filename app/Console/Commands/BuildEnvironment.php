<?php

namespace App\Console\Commands;

use Database\Migrations\CreateActions;
use Atsmacode\CardGames\Database\Migrations\CreateCards;
use Database\Migrations\CreateDatabase;
use Database\Migrations\CreateHands;
use Database\Migrations\CreateHandTypes;
use Database\Migrations\CreatePlayerActionLogs;
use Database\Migrations\CreatePlayerActions;
use Database\Migrations\CreatePlayers;
use Database\Migrations\CreatePots;
use Database\Migrations\CreateStacks;
use Database\Migrations\CreateStreets;
use Database\Migrations\CreateTables;
use Database\Migrations\CreateWholeCards;
use Database\Seeders\SeedActions;
use Atsmacode\CardGames\Database\Seeders\SeedCards;
use Database\Seeders\SeedHandTypes;
use Database\Seeders\SeedPlayers;
use Database\Seeders\SeedStreets;
use Database\Seeders\SeedTables;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

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
        CreateStacks::class,
        CreatePots::class,
        SeedCards::class,
        SeedHandTypes::class,
        SeedTables::class,
        SeedPlayers::class,
        SeedStreets::class,
        SeedActions::class,
        CreatePlayerActionLogs::class
    ];
    protected static $defaultName = 'app:build-env';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('-v', InputArgument::OPTIONAL, 'Display feedback message in console.');
        $this->addOption('-d', '-d', InputArgument::OPTIONAL, 'Run in dev mode for running unit tests.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $GLOBALS['THE_ROOT'] = '';
        
        unset($GLOBALS['dev']);
        $dev    = $input->getOption('-d') === 'true' ?: false;
        $GLOBALS['dev'] = $dev ? $dev : null; 

        foreach($this->buildClasses as $class){
            foreach($class::$methods as $method){
                (new $class())->{$method}();
            }
        }

        return Command::SUCCESS;
    }
}
