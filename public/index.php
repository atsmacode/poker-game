<?php

use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PotLimitHoldEmHandController;
use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\PlayerActionController as PotLimitHoldEmPlayerActionController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\HandController as PotLimitOmahaHandController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\PlayerActionController as PotLimitOmahaPlayerActionController;
use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\Game\PotLimitOmaha;
use Symfony\Component\HttpFoundation\Request;

require('../vendor/autoload.php');
require('../config/container.php');

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (str_contains($_SERVER['REQUEST_URI'], 'play/plhe')) {
        echo $serviceManager->build(PotLimitHoldEmHandController::class, ['game' => PotLimitHoldEm::class])->play();
    }
    
    if (str_contains($_SERVER['REQUEST_URI'], 'play/plom')) {
        echo $serviceManager->build(PotLimitOmahaHandController::class, ['game' => PotLimitHoldEm::class])->play();
    }
}

/** The requests are empty, this is here just to test the container dependencies */
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = Request::create(
        uri: '',
        method: 'POST',
        content: json_encode([])
    );

    if (str_contains($_SERVER['REQUEST_URI'], 'action/plhe')) {
        echo $serviceManager->build(
            PotLimitHoldEmPlayerActionController::class,
            ['game' => PotLimitHoldEm::class]
        )->action($request);
    }
    
    if (str_contains($_SERVER['REQUEST_URI'], 'action/plom')) {
        echo $serviceManager->build(
            PotLimitOmahaPlayerActionController::class,
            ['game' => PotLimitOmaha::class]
        )->action($request);
    }
}

