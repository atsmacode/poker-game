<?php

require './vendor/autoload.php';
require './config/container.php';

use Atsmacode\CardGames\Console\Commands\BuildCardGames;
use Atsmacode\PokerGame\Console\Commands\BuildPokerGame;
use Atsmacode\Framework\Console\Commands\CreateDatabase;
use Atsmacode\PokerGame\Database\DbalTestFactory;
use Atsmacode\PokerGame\Database\PdoTestFactory;
use Symfony\Component\Console\Application;

$dbalTest = new DbalTestFactory();
$pdoTest  = new PdoTestFactory();

$application = new Application();
$application->add(new CreateDatabase(null, $serviceManager, $dbalTest, $pdoTest));
$application->add(new BuildCardGames(null, $serviceManager, $dbalTest, $pdoTest));
$application->add(new BuildPokerGame(null, $serviceManager, $dbalTest, $pdoTest));
$application->run();
