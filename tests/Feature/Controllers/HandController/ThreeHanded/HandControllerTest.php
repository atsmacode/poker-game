<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\HandController\ThreeHanded;

use Atsmacode\PokerGame\Controllers\HandController;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;

class HandControllerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Test Table', 'seats' => 3]);

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
    }

    /**
     * @test
     * @return void
     */
    public function it_can_start_the_game()
    {
        $response = $this->jsonResponse();

        // The small blind was posted
        $this->assertEquals(25, $response['players'][1]['bet_amount']);
        $this->assertEquals('Bet', $response['players'][1]['action_name']);

        // The big blind was posted
        $this->assertEquals(50, $response['players'][2]['bet_amount']);
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
    public function the_pre_flop_action_will_initially_be_on_player_one()
    {
        $response = $this->jsonResponse();

        $this->assertTrue($response['players'][0]['action_on']);
    }

    private function jsonResponse(): array
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $response = (new HandController())->play($this->table->id);

        return json_decode($response, true)['body'];
    }
}
