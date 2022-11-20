<?php

namespace Tests\Feature\Controllers\PlayerActionController\ActionOptions;

use App\Classes\ActionHandler\ActionHandler;
use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Constants\Action;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;
use Tests\Feature\HasActionPosts;
use Tests\Feature\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand     = Hand::create(['table_id' => $this->table->id]);
        $this->gamePlay = new GamePlay($this->hand);

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

        $this->gameState     = new GameState($this->hand);
        $this->actionHandler = new ActionHandler($this->gameState);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_raise_can_fold_call_or_raise()
    {
        $this->gamePlay->start(null, $this->gameState);

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
        $this->gamePlay->start(null, $this->gameState);

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
        $this->gamePlay->start(null, $this->gameState);

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
        $this->gamePlay->start(null, $this->gameState);

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
        
        $this->gamePlay->start(null, $this->gameState);

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
        $this->gamePlay->start(null, $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

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
