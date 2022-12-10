<?php

namespace Atsmacode\PokerGame\Tests\Unit;

use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;

trait HasGamePlay
{
    private function createPlayer(int $player)
    {
        $playerModel = $this->container->build(Player::class);

        return $playerModel->create([
            'name'  => 'Player ' . $player,
            'email' => sprintf('player%d@rrh.com', $player)
        ]);
    }

    private function setGamePlay()
    {
        $this->gameState = $this->container->build(GameState::class, [
            'hand' => $this->hand,
        ]);
        
        $this->gamePlay  = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get(PotLimitHoldEm::class),
            'gameState' => $this->gameState,
        ]);
    }

    private function isThreeHanded()
    {
        $this->tableModel     = $this->container->get(Table::class);
        $this->handModel      = $this->container->get(Hand::class);
        $this->playerModel    = $this->container->get(Player::class);
        $this->tableSeatModel = $this->container->get(TableSeat::class);

        $this->table = $this->tableModel->create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand  = $this->handModel->create(['table_id' => $this->table->id]);

        $this->player1 = $this->createPlayer(1);
        $this->player2 = $this->createPlayer(2);
        $this->player3 = $this->createPlayer(3);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->setGamePlay();
    }
}