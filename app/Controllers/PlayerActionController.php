<?php

namespace Atsmacode\PokerGame\Controllers;

use Atsmacode\PokerGame\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\Models\Hand;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\ServiceManager\ServiceManager;

abstract class PlayerActionController
{
    /**
     * To be set to the fully qualified class name of an 
     * implementation of the Game interface.
     */
    protected string $game = '';

    public function __construct(
        private ServiceManager $container,
        ActionHandler $actionHandler
    ) {
        $this->actionHandler = $actionHandler;
        $this->handModel     = $container->get(Hand::class);
    }

    public function action(): JsonResponse
    {
        $requestBody = file_get_contents('php://input')
            ? json_decode(file_get_contents('php://input'), true)['body']
            : $_POST['body'];

        $hand      = $this->handModel->latest();
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

        $gamePlayService = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get($this->game),
            'gameState' => $gameState,
            'deck'      => $requestBody['deck'],
        ]);
        $gamePlay = $gamePlayService->play($gameState);

        return new JsonResponse([
            'deck'           => $gamePlay['deck'],
            'pot'            => $gamePlay['pot'],
            'communityCards' => $gamePlay['communityCards'],
            'players'        => $gamePlay['players'],
            'winner'         => $gamePlay['winner']
        ]);
    }
}
