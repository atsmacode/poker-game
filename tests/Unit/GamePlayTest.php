<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Hand;
use PHPUnit\Framework\TestCase;

class GamePlayTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_start_the_game()
    {
        $response = $this->gamePlay->start();

        // There are 4 players at the table
        $this->assertCount(4, $this->gamePlay->hand->playerActions->fresh());

        // The small blind was posted
        $this->assertEquals(25.0, $this->gamePlay->hand->playerActions->fresh()->slice(1, 1)->first()->bet_amount);
        $this->assertEquals('Bet', $this->gamePlay->hand->playerActions->fresh()->slice(1, 1)->first()->action->name);

        // The big blind was posted
        $this->assertEquals(50.0, $this->gamePlay->hand->playerActions->fresh()->slice(2, 1)->first()->bet_amount);
        $this->assertEquals('Bet', $this->gamePlay->hand->playerActions->fresh()->slice(2, 1)->first()->action->name);

        // The last player at the table has not acted yet
        $this->assertEquals(null, $this->gamePlay->hand->playerActions->fresh()->slice(3, 1)->first()->bet_amount);
        $this->assertEquals(null, $this->gamePlay->hand->playerActions->fresh()->slice(3, 1)->first()->action_id);

        // Each player in the hand has 2 whole cards
        foreach($this->gamePlay->handTable->fresh()->players as $player){
            $this->assertCount(2, $player->wholeCards->where('hand_id', $this->gamePlay->hand->fresh()->id));
        }

        // the_action_will_be_on_the_player_after_the_big_blind_once_a_hand_is_started
        $this->assertTrue($response['players'][3]['action_on']);

    }

}