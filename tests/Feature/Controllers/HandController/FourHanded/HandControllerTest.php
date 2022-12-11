<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\HandController\FourHanded;

use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class HandControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isFourHanded();
    }

    /**
     * @test
     * @return void
     */
    public function it_can_start_the_game()
    {
        $response = $this->handControllerResponse();

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
    public function the_pre_flop_action_will_initially_be_on_the_player_four()
    {
        $response = $this->handControllerResponse();

        $this->assertTrue($response['players'][3]['action_on']);
    }
}
