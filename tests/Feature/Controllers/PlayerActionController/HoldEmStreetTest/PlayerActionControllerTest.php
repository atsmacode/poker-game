<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\HoldEmStreetTest;

use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;
use Atsmacode\PokerGame\Tests\HasStreets;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts, HasStreets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();
    }

     /**
     * @test
     * @return void
     */
    public function it_can_deal_3_cards_to_a_flop()
    {
        $this->gamePlay->start();

        $this->executeActionsToContinue();

        $this->actionControllerResponse();

        $this->assertCount(2, $this->handStreetModel->find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(3, $this->handStreetModel->getStreetCards($this->gameState->handId(), 2));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_1_card_to_a_turn()
    {
        $this->gamePlay->start();

        $this->setFlop();

        $this->executeActionsToContinue();

        $this->actionControllerResponse();

        $this->assertCount(3, $this->handStreetModel->find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(1, $this->handStreetModel->getStreetCards($this->gameState->handId(), 3));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_1_card_to_a_river()
    {
        $this->gamePlay->start();

        $this->setFlop();

        $this->setTurn();

        $this->executeActionsToContinue();

        $this->actionControllerResponse();

        $this->assertCount(4, $this->handStreetModel->find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(1, $this->handStreetModel->getStreetCards($this->gameState->handId(), 4));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_reach_showdown_when_all_active_players_can_continue_on_the_river()
    {
        $this->gamePlay->start();

        $this->setFlop();

        $this->setTurn();

        $this->setRiver();

        $this->executeActionsToContinue();

        $response = $this->actionControllerResponse();

        $this->assertNotNull($response['winner']);
    }

    protected function executeActionsToContinue()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->setPlayerThreeChecksPost();
    }
}
