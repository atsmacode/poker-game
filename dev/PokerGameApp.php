<?php

require './vendor/autoload.php';

use Atsmacode\CardGames\Console\Commands\BuildCardGames;
use Atsmacode\PokerGame\Console\Commands\BuildPokerGame;
use Atsmacode\PokerGame\PokerGameConfigProvider;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new BuildCardGames(null, new PokerGameConfigProvider()));
$application->add(new BuildPokerGame(null, new PokerGameConfigProvider()));
$application->run();
