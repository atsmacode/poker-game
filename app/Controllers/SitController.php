<?php

namespace Atsmacode\PokerGame\Controllers;

use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\SitHandler\SitHandler;
use Laminas\ServiceManager\ServiceManager;
use Symfony\Component\HttpFoundation\Response;

abstract class SitController
{ 
    /**
     * To be set to the fully qualified class name of an 
     * implementation of the Game interface.
     */
    protected string $game = '';

    private Hand       $handModel;
    private Table      $tableModel;
    private SitHandler $sitHandler;
    private Player     $playerModel;

    public function __construct(private ServiceManager $container)
    {
        $this->handModel   = $container->build(Hand::class);
        $this->tableModel  = $container->get(Table::class);
        $this->sitHandler  = $container->get(SitHandler::class);
        $this->playerModel = $container->get(Player::class);
    }

    public function sit(
        ?int $tableId = null,
        ?TableSeat $currentDealer = null,
        ?int $playerId = null
    ): Response {
        if (null !== $playerId) {
            $playerSeat = $this->sitHandler->sit($playerId);
            $tableId    = $playerSeat->getId();

            if (2 > count($this->tableModel->hasMultiplePlayers($tableId))) {
                return new Response(json_encode([
                    'message' => 'Waiting for more players to join.',
                    'players' => $this->setWaitingPlayerData($playerId, $playerSeat->getId())
                ]));
            }
        }

        $hand = $this->handModel->create(['table_id' => $tableId ?? 1]);

        $gamePlayService = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get($this->game),
            'gameState' => $this->container->build(GameState::class, ['hand' => $hand])
        ]);
        $gamePlay = $gamePlayService->start($currentDealer ?? null);

        return new Response(json_encode([
            'deck'           => $gamePlay['deck'],
            'pot'            => $gamePlay['pot'],
            'communityCards' => $gamePlay['communityCards'],
            'players'        => $gamePlay['players'],
            'winner'         => $gamePlay['winner']
        ]));
    }

    private function setWaitingPlayerData(int $playerId, int $tableSeatId,)
    {
        $playerName =  $this->playerModel->find(['id' => $playerId])->getName();

        return array(
            $tableSeatId => [
                'stack'            => null,
                'name'             => $playerName,
                'action_id'        => null,
                'action_name'      => null,
                'player_id'        => $playerId,
                'table_seat_id'    => $tableSeatId,
                'hand_street_id'   => null,
                'bet_amount'       => null,
                'active'           => 0,
                'can_continue'     => 0,
                'is_dealer'        => 0,
                'big_blind'        => 0,
                'small_blind'      => 0,
                'whole_cards'      => [],
                'action_on'        => false,
                'availableOptions' => []
            ]
        );
    }
}
