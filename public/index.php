<?php

use App\Controllers\HandController;
use App\Controllers\PlayerActionController;

if (!isset($GLOBALS['dev'])) {
    $GLOBALS['THE_ROOT'] = '../';
}

require_once($GLOBALS['THE_ROOT'] . 'vendor/autoload.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (str_contains($_SERVER['REQUEST_URI'], 'play')) {
        return new HandController();
    }

    if (str_contains($_SERVER['REQUEST_URI'], 'action')) {
        return new PlayerActionController();
    }
}

if (str_contains($_SERVER['REQUEST_URI'], 'play')) {
    require($GLOBALS['THE_ROOT'] . 'resources/play.php');
} else {
    require($GLOBALS['THE_ROOT'] . 'resources/index.php');
}
