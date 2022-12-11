<?php

use Atsmacode\PokerGame\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PotLimitHoldEmHandController;
use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\PlayerActionController as PotLimitHoldEmPlayerActionController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\HandController as PotLimitOmahaHandController;
use Atsmacode\PokerGame\Controllers\PotLimitOmaha\PlayerActionController as PotLimitOmahaPlayerActionController;

$GLOBALS['THE_ROOT'] = '../';

require('../vendor/autoload.php');
require('../config/container.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (str_contains($_SERVER['REQUEST_URI'], 'play/plhe')) {
        return (new PotLimitHoldEmHandController($serviceManager))->play();
    }

    if (str_contains($_SERVER['REQUEST_URI'], 'play/plom')) {
        return (new PotLimitOmahaHandController($serviceManager))->play();
    }

    if (str_contains($_SERVER['REQUEST_URI'], 'action/plhe')) {
        return (new PotLimitHoldEmPlayerActionController(
            $serviceManager,
            $serviceManager->get(ActionHandler::class)
        ))->action();
    }

    if (str_contains($_SERVER['REQUEST_URI'], 'action/plom')) {
        return (new PotLimitOmahaPlayerActionController(
            $serviceManager,
            $serviceManager->get(ActionHandler::class)
        ))->action();
    }
}
