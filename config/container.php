<?php

use Laminas\ServiceManager\ServiceManager;

$dependencyMap  = require_once('dependencies.php');
$serviceManager = new ServiceManager($dependencyMap);

// $gamePlay = $serviceManager->build(GamePlay::class, [
//     'game' => $serviceManager->get(Atsmacode\PokerGame\Classes\Game\PotLimitOmaha::class)
// ]);

// $gamePlay2 = $serviceManager->build(GamePlay::class, [
//     'game' => $serviceManager->get(Atsmacode\PokerGame\Classes\Game\PotLimitHoldEm::class)
// ]);

// var_dump($gamePlay);