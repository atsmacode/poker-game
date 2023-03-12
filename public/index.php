<?php

use Atsmacode\PokerGame\Controllers\Player\Controller as PlayerController;
use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PlheHandController;
use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\PlayerActionController as PlhePlayerActionController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\HandController as PlomHandController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\PlayerActionController as PlomPlayerActionController;
use Symfony\Component\HttpFoundation\Request;

require('../vendor/autoload.php');
require('../config/container.php');

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (str_contains($_SERVER['REQUEST_URI'], 'play/plhe')) {
        echo $serviceManager->get(PlheHandController::class)->play();
    }
    
    if (str_contains($_SERVER['REQUEST_URI'], 'play/plom')) {
        echo $serviceManager->get(PlomHandController::class)->play();
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = Request::createFromGlobals();

    if (str_contains($_SERVER['REQUEST_URI'], 'action/plhe')) {
        echo $serviceManager->get(PlhePlayerActionController::class)->action($request);
    }
    
    if (str_contains($_SERVER['REQUEST_URI'], 'action/plom')) {
        echo $serviceManager->get(PlomPlayerActionController::class)->action($request);
    }

    if (str_contains($_SERVER['REQUEST_URI'], '/users')) {
        echo $serviceManager->get(PlayerController::class)->create($request);
    }
}

