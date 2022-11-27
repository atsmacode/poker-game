<?php

namespace Atsmacode\PokerGame\Controllers;

use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Atsmacode\PokerGame\Models\Hand;

class HandController
{
    public function play($tableId = null, $currentDealer = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hand     = Hand::create(['table_id' => $tableId ?? 1]);
            $gamePlay = (new GamePlay(
                new GameState(new GameData(), $hand),
                new Start(),
                new NewStreet(),
                new Showdown(),
                new PlayerHandler()
            ))->start(
                $currentDealer ?? null
            );

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
