<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\ActionOptions;

use Atsmacode\PokerGame\Classes\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\Feature\HasActionPosts;
use Atsmacode\PokerGame\Tests\Feature\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand     = Hand::create(['table_id' => $this->table->id]);

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

        $this->gameState = new GameState(new GameData(), $this->hand);
        $this->gamePlay  = new GamePlay(
            $this->gameState,
            new Start(),
            new NewStreet(),
            new Showdown(),
            new PlayerHandler()
        );

        $this->actionHandler = new ActionHandler($this->gameState);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_raise_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        $this->setPlayerFourRaisesPost();

        $response = $this->jsonResponse();

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

        $response = $this->jsonResponse();

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

        $response = $this->jsonResponse();

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

        $response = $this->jsonResponse();

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

        $response = $this->jsonResponse();

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

        $this->assertCount(1, $this->gameState->updateHandStreets()->getHandStreets()->content);

        $this->givenActionsMeanNewStreetIsDealt();

        $response = $this->jsonResponse();

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
