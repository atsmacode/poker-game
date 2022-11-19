<?php

namespace Tests\Feature\Controllers\HandController\SixHanded;

use App\Controllers\HandController;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;
use Tests\Feature\HasActionPosts;
use Tests\Feature\HasGamePlay;

class HandControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Test Table', 'seats' => 6]);

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

        $this->player4 = Player::create([
            'name' => 'Player 4',
            'email' => 'player4@rrh.com'
        ]);

        $this->player5 = Player::create([
            'name' => 'Player 5',
            'email' => 'player5@rrh.com'
        ]);

        $this->player6 = Player::create([
            'name' => 'Player 6',
            'email' => 'player6@rrh.com'
        ]);

        $this->seat1 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->seat2 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->seat3 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->seat4 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player4->id
        ]);

        $this->seat5 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player5->id
        ]);

        $this->seat6 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player6->id
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function the_pre_flop_action_will_initially_be_on_player_four()
    {
        $response = $this->jsonResponse();

        $this->assertTrue($response['players'][3]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function if_there_are_two_seats_after_current_dealer_big_blind_will_be_seat_one()
    {
        $currentDealer = $this->seat4;

        $response = $this->jsonResponse($currentDealer);

        $this->assertEquals(1, $response['players'][5]['small_blind']);
        $this->assertEquals(1, $response['players'][0]['big_blind']);
    }

    /**
     * @test
     * @return void
     */
    public function if_there_is_one_seat_after_current_dealer_big_blind_will_be_seat_two()
    {
        $currentDealer = $this->seat5;

        $response = $this->jsonResponse($currentDealer);

        $this->assertEquals(1, $response['players'][0]['small_blind']);
        $this->assertEquals(1, $response['players'][1]['big_blind']);
    }

    private function jsonResponse(TableSeat $currentDealer = null): array
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $response = (new HandController())->play($this->table->id, $currentDealer);

        return json_decode($response, true)['body'];
    }
}
