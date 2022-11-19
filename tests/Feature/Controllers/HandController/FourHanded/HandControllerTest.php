<?php

namespace Tests\Feature\Controllers\HandController\FourHanded;

use App\Controllers\HandController;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class HandControllerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Test Table', 'seats' => 4]);

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

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player4->id
        ]); 
    }

    /**
     * @test
     * @return void
     */
    public function it_can_start_the_game()
    {
        $response = $this->jsonResponse();

        // The small blind was posted
        $this->assertEquals(25.0, $response['players'][1]['bet_amount']);
        $this->assertEquals('Bet', $response['players'][1]['action_name']);

        // The big blind was posted
        $this->assertEquals(50.0, $response['players'][2]['bet_amount']);
        $this->assertEquals('Bet', $response['players'][2]['action_name']);

        // The dealer, seat 1, has not acted yet
        $this->assertEquals(null, $response['players'][0]['bet_amount']);
        $this->assertEquals(null, $response['players'][0]['action_id']);

        // Each player in the hand has 2 whole cards
        foreach($response['players'] as $player){
            $this->assertCount(2, $player['whole_cards']);
        }
    }

    /**
     * @test
     * @return void
     */
    public function the_pre_flop_action_will_initially_be_on_the_player_four()
    {
        $response = $this->jsonResponse();

        $this->assertTrue($response['players'][3]['action_on']);
    }

    private function jsonResponse(): array
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $response = (new HandController())->play($this->table->id);

        return json_decode($response, true)['body'];
    }
}
