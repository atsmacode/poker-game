<?php

namespace Atsmacode\PokerGame\Tests\Unit;

use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\GameData\GameData;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;

class TableSeatTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->table     = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand      = Hand::create(['table_id' => $this->table->id]);
        $this->tableSeat = $this->container->get(TableSeat::class);

        $this->player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        $this->player3 = Player::create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        $this->tableSeat->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->tableSeat->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->tableSeat->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->gameState = new GameState(new GameData(), $this->hand);
        $this->gamePlay  = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get(PotLimitHoldEm::class),
            'gameState' => $this->gameState
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function a_table_seat_can_be_updated()
    {
        $tableSeat = $this->tableSeat->find(['id' => $this->gameState->getSeats()[0]['id']]);

        $this->assertEquals(0, $tableSeat->can_continue);

        $tableSeat->update(['can_continue' => 1]);

        $this->assertEquals(1, $tableSeat->can_continue);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_select_first_active_player_after_dealer()
    {
        $this->gamePlay->start($this->tableSeat->find([
            'id' => $this->gameState->getSeats()[0]['id']
        ]));

        $tableSeat = $this->tableSeat->playerAfterDealer(
            $this->gameState->handId(),
            $this->gameState->getSeats()[0]['id']
        );

        $this->assertEquals($this->gameState->getSeats()[1]['id'], $tableSeat->id);
    }
}
