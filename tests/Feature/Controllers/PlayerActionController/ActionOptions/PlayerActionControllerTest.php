<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\ActionOptions;

use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
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
    public function a_player_facing_a_raise_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        $this->setPlayerFourRaisesPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][0]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][0]['availableOptions']);
        $this->assertContains(Action::CALL, $response['players'][0]['availableOptions']);
        $this->assertContains(Action::RAISE, $response['players'][0]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_raise_fold_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        $this->givenPlayerFourRaises();
        $this->setPlayerOneFoldsPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][1]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][1]['availableOptions']);
        $this->assertContains(Action::CALL, $response['players'][1]['availableOptions']);
        $this->assertContains(Action::RAISE, $response['players'][1]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_folded_player_has_no_options()
    {
        $this->gamePlay->start();

        $this->setPlayerFourFoldsPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][0]['action_on']);
        $this->assertEmpty($response['players'][3]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_facing_a_call_can_fold_check_or_raise()
    {
        $this->gamePlay->start();

        $this->setPlayerTwoCallsPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][2]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::CHECK, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::RAISE, $response['players'][2]['availableOptions']);
    }
    /**
     * @test
     * @return void
     */
    public function the_big_blind_facing_a_call_fold_can_fold_check_or_raise()
    {
        $this->gamePlay->start();

        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->setPlayerTwoFoldsPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][2]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::CHECK, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::RAISE, $response['players'][2]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_call_can_fold_call_or_raise()
    {
        
        $this->gamePlay->start();

        $this->setPlayerFourCallsPost();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][0]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][0]['availableOptions']);
        $this->assertContains(Action::CALL, $response['players'][0]['availableOptions']);
        $this->assertContains(Action::RAISE, $response['players'][0]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function the_first_active_player_on_a_new_street_can_fold_check_or_bet()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gameState->updateHandStreets()->getHandStreets());

        $this->givenActionsMeanNewStreetIsDealt();

        $response = $this->actionControllerResponse();

        $this->assertTrue($response['players'][2]['action_on']);

        $this->assertContains(Action::FOLD, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::CHECK, $response['players'][2]['availableOptions']);
        $this->assertContains(Action::BET, $response['players'][2]['availableOptions']);
    }

    private function givenActionsMeanNewStreetIsDealt()
    {
        $this->givenPlayerFourCalls();
        $this->givenPlayerFourCanContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->setPlayerThreeChecksPost();
    }
}
