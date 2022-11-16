<?php

namespace App\Controllers;

use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Models\Hand;

class HandController
{
    public function play()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $gamePlay  = (new GamePlay(Hand::create(['table_id' => 1])))->start(null, new GameState());

            if (!isset($GLOBALS['dev'])) {
                header("Content-Type: application/json");
                http_response_code(200);
            }

            $responseBody = serialize([
                'deck'           => $gamePlay['deck'],
                'pot'            => $gamePlay['pot'],
                'communityCards' => $gamePlay['communityCards'],
                'players'        => $gamePlay['players'],
                'winner'         => $gamePlay['winner']
            ]);

            if (isset($GLOBALS['dev'])) {
                return json_encode(['body' => $responseBody]);
            } else {
                echo json_encode(['body' => unserialize($responseBody)]);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
            include($GLOBALS['THE_ROOT'] . 'public/index.php'); 
        }
    }
}
