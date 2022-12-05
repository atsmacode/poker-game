<?php

namespace Atsmacode\PokerGame\Console\Commands;

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
use Atsmacode\PokerGame\DbalTestFactory;
use Doctrine\DBAL\Connection;
use Laminas\ServiceManager\ServiceManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'app:build-poker-game',
    description: 'Populate the DB with all resources',
    hidden: false,
    aliases: ['app:build-poker-game']
)]

class BuildPokerGame extends Command
{
    private $buildClasses = [
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

    public function __construct(string $name = null, ServiceManager $container)
    {
        parent::__construct($name);

        $this->container = $container;
    }

    protected function configure(): void
    {
        $this->addArgument('-v', InputArgument::OPTIONAL, 'Display feedback message in console.');
        $this->addOption('-d', '-d', InputArgument::OPTIONAL, 'Run in dev mode for running unit tests.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dev = $input->getOption('-d') === 'true' ?: false;

        if ($dev) { $this->container->setFactory(Connection::class, new DbalTestFactory()); }

        $connection = $this->container->get(Connection::class);

        var_dump($connection);

        foreach($this->buildClasses as $class){
            foreach($class::$methods as $method){
                (new $class($connection))->{$method}();
            }
        }

        return Command::SUCCESS;
    }
}
