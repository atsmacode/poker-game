<?php

namespace App\Controllers;

use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Models\Hand;

class HandController
{
    public function play($tableId = null, $currentDealer = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hand     = Hand::create(['table_id' => $tableId ?? 1]);
            $gamePlay = (new GamePlay())->start(new GameState($hand), $currentDealer ?? null);

            if (!isset($GLOBALS['dev'])) {
                header("Content-Type: application/json");
                http_response_code(200);
            }

            $responseBody = [
                'deck'           => $gamePlay['deck'],
                'pot'            => $gamePlay['pot'],
                'communityCards' => $gamePlay['communityCards'],
                'players'        => $gamePlay['players'],
                'winner'         => $gamePlay['winner']
            ];

            if (isset($GLOBALS['dev'])) {
                return json_encode(['body' => $responseBody]);
            } else {
                echo json_encode(['body' => $responseBody]);
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') { 
            include($GLOBALS['THE_ROOT'] . 'public/index.php'); 
        }
    }
}
