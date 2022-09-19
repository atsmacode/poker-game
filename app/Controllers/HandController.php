<?php

namespace App\Controllers;

if (!isset($GLOBALS['dev'])) {
    require_once('../../vendor/autoload.php');
}

use App\Classes\GamePlay;
use App\Models\Hand;

class HandController
{
    public function play()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $gamePlay = (new GamePlay(Hand::create(['table_id' => 1])))->start();

            if (!isset($GLOBALS['dev'])) {
                header("Content-Type: application/json");
                http_response_code(200);
            }

            return json_encode([
                'deck'           => $gamePlay['deck'],
                'pot'            => $gamePlay['pot'],
                'communityCards' => $gamePlay['communityCards'],
                'players'        => $gamePlay['players'],
                'winner'         => $gamePlay['winner']
            ]);
        }

        if (!isset($GLOBALS['dev'])) {
            return include('../../index.php'); 
        } else {
            return include('public/index.php'); 
        }
    }
}

return (new HandController())->play();
