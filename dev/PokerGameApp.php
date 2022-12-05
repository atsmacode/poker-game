<?php

require './vendor/autoload.php';
require './config/container.php';

use Atsmacode\CardGames\Console\Commands\BuildCardGames;
use Atsmacode\PokerGame\Console\Commands\BuildPokerGame;
use Atsmacode\PokerGame\PokerGameConfigProvider;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new BuildCardGames(null, new PokerGameConfigProvider()));
$application->add(new BuildPokerGame(null, $serviceManager));
$application->run();
