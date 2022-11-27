<?php

namespace Atsmacode\PokerGame\Console\Commands;

use Atsmacode\PokerGame\Database\Migrations\CreateActions;
use Atsmacode\CardGames\Database\Migrations\CreateCards;
use Atsmacode\Orm\Database\Migrations\CreateDatabase;
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
use Atsmacode\CardGames\Database\Seeders\SeedCards;
use Atsmacode\PokerGame\Database\Seeders\SeedHandTypes;
use Atsmacode\PokerGame\Database\Seeders\SeedPlayers;
use Atsmacode\PokerGame\Database\Seeders\SeedStreets;
use Atsmacode\PokerGame\Database\Seeders\SeedTables;
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
