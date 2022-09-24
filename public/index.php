<?php

use App\Controllers\PlayerActionController;

require_once($GLOBALS['THE_ROOT'] . 'vendor/autoload.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (str_contains($_SERVER['REQUEST_URI'], 'action')) {
        return new PlayerActionController();
    }
}

require($GLOBALS['THE_ROOT'] . 'resources/index.php');
