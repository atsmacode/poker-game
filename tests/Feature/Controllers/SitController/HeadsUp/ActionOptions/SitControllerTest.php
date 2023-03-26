<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\SitController\HeadsUp\ActionOptions;

use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;
use Atsmacode\PokerGame\Tests\HasStreets;

class SitControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts, HasStreets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isHeadsUp()
            ->setHand()
            ->setGamePlay();
    }

    /**
     * @test
     * @return void
     */
    public function theBigBlindWillBeFirstToActOnTheFlop()
    {
        $this->gamePlay->start();

        $this->setFlop();

        $response = $this->sitControllerResponseWithPlayerId(playerId: $this->playerOne->getId());

        $this->assertEquals(true, $response['players'][2]['action_on']);
    }
}
