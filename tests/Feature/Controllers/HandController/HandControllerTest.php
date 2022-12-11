<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\HandController;

use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PotLimitHoldEmHandController;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class HandControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts;

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

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player5->id
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player6->id
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function it_returns_valid_response_keys_on_post_request()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $response = $this->jsonResponse();

        $this->assertEquals(
            $this->validResponseKeys(),
            array_keys($response)
        );
    }

    /**
     * @test
     * @return void
     */
    public function with_blinds_25_and_50_the_pot_size_will_be_75_once_the_hand_is_started()
    {
        $response = $this->jsonResponse();

        $this->assertEquals(75, $response['pot']);
    }

    /**
     * This test wasn't right, not sure how to
     * test the GET response as it simply includes
     * the file. TODO when improving routes & responses.
     */
    // public function it_returns_index_on_get_request()
    // {
    //     $_SERVER['REQUEST_METHOD'] = 'GET';
    //     $_SERVER['REQUEST_URI']    = 'index.php/play';

    //     $controller = new HandController();
    //     $response   = $controller->play();

    //     $this->assertEquals(include('resources/index.php'), $response);
    // }

    public function validResponseKeys()
    {
        return [
            'deck',
            'pot',
            'communityCards',
            'players',
            'winner'
        ];
    }

    private function jsonResponse(): array
    {
        $response = (new PotLimitHoldEmHandController($this->container))->play($this->table->id);

        return json_decode($response, true)['body'];
    }
}
