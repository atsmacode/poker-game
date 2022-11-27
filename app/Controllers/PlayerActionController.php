<?php

namespace Atsmacode\PokerGame\Controllers;

use Atsmacode\PokerGame\Classes\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Atsmacode\PokerGame\Models\Hand;

class PlayerActionController
{
    public function __construct(
        ActionHandler $actionHandler
    ) {
        $this->actionHandler = $actionHandler;
    }

    public function action()
    {
        $requestBody = file_get_contents('php://input')
            ? json_decode(file_get_contents('php://input'), true)['body']
            : $_POST['body'];

        $hand      = Hand::latest();
        $gameState = $this->actionHandler->handle(
            $hand,
            $requestBody['player_id'],
            $requestBody['table_seat_id'],
            $requestBody['hand_street_id'],
            $requestBody['bet_amount'],
            $requestBody['action_id'],
            $requestBody['active'],
            $requestBody['stack']
        );

        $gamePlay = (new GamePlay(
            $gameState,
            new Start(),
            new NewStreet(),
            new Showdown(),
            new PlayerHandler(),
            $requestBody['deck']
        ))->play($gameState);

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

        /** @todo Remove all isset($dev)s */
        if (isset($GLOBALS['dev'])) {
            return json_encode(['body' => $responseBody]);
        } else {
            echo json_encode(['body' => $responseBody]);
        }
    }
}
