<?php

namespace Atsmacode\PokerGame\Tests\Unit;

use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\GameData\GameData;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\HandStep\NewStreet;
use Atsmacode\PokerGame\HandStep\Showdown;
use Atsmacode\PokerGame\HandStep\Start;
use Atsmacode\PokerGame\PlayerHandler\PlayerHandler;
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

        $this->table = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand  = Hand::create(['table_id' => $this->table->id]);

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

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->gameState = new GameState(new GameData(), $this->hand);
        $this->gamePlay  = new GamePlay(
            $this->gameState,
            new PotLimitHoldEm(),
            new Start(),
            new NewStreet(),
            new Showdown(),
            new PlayerHandler()
        );
    }

    /**
     * @test
     * @return void
     */
    public function a_table_seat_can_be_updated()
    {
        $tableSeat = TableSeat::find(['id' => $this->gameState->getSeats()[0]['id']]);

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
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gameState->getSeats()[0]['id']
        ]));

        $tableSeat = TableSeat::playerAfterDealer(
            $this->gameState->handId(),
            $this->gameState->getSeats()[0]['id']
        );

        $this->assertEquals($this->gameState->getSeats()[1]['id'], $tableSeat->id);
    }
}
