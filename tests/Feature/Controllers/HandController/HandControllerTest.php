<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\HandController;

use Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController as PotLimitHoldEmHandController;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class HandControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isSixHanded();
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

        return json_decode($response->getBody()->getContents(), true);
    }
}
