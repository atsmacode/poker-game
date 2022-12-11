<?php

namespace Atsmacode\PokerGame\Controllers;

use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Laminas\ServiceManager\ServiceManager;

abstract class HandController
{
    /**
     * To be set to the fully qualified class name of an 
     * implementation of the Game interface.
     */
    protected string $game = '';

    public function __construct(private ServiceManager $container)
    {
        $this->handModel = $container->build(Hand::class);
    }

    public function play($tableId = null, $currentDealer = null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hand = $this->handModel->create(['table_id' => $tableId ?? 1]);

            $gamePlayService = $this->container->build(GamePlay::class, [
                'game'      => $this->container->get($this->game),
                'gameState' => $this->container->build(GameState::class, ['hand' => $hand])
            ]);
            $gamePlay = $gamePlayService->start($currentDealer ?? null);

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
