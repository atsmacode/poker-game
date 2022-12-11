<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\HandController\ThreeHanded;

use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PotLimitHoldEmHandController;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class HandControllerTest extends BaseTest
{
    use HasGamePlay;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();
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

        $response = (new PotLimitHoldEmHandController($this->container))->play($this->table->id);

        return json_decode($response->getBody()->getContents(), true);
    }
}
