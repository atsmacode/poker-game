<?php

namespace Tests\Feature\GamePlay;

use App\Classes\GamePlay\GamePlay;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class GamePlaySixHandedTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 6]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => $this->table ->id]));

        $this->player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player3@rrh.com'
        ]);

        $this->player3 = Player::create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        $this->player4 = Player::create([
            'name' => 'Player 5',
            'email' => 'player5@rrh.com'
        ]);

        $this->player5 = Player::create([
            'name' => 'Player 6',
            'email' => 'player3@rrh.com'
        ]);

        $this->player6 = Player::create([
            'name' => 'Player 6',
            'email' => 'player6@rrh.com'
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player3->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player4->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player6->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player6->id
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function if_there_are_two_seats_after_current_dealer_big_blind_will_be_seat_one()
    {
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $response = $this->gamePlay->play();

        $this->assertEquals(1, $response['players'][5]['small_blind']);
        $this->assertEquals(1, $response['players'][0]['big_blind']);
    }

    /**
     * @test
     * @return void
     */
    public function if_there_is_one_seat_after_current_dealer_big_blind_will_be_seat_two()
    {
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(4, 1)->id
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $response = $this->gamePlay->play();

        $this->assertEquals(1, $response['players'][0]['small_blind']);
        $this->assertEquals(1, $response['players'][1]['big_blind']);
    }
}
