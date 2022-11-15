<?php

namespace Tests\Feature\Controllers;

use App\Classes\ActionHandler\ActionHandler;
use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Controllers\PlayerActionController;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;
use Tests\Feature\GamePlay\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table         = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->gamePlay      = new GamePlay(Hand::create(['table_id' => $this->table->id]));
        $this->gameState     = new GameState();
        $this->actionHandler = new ActionHandler($this->gameState);

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
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player3->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player4->id
        ]); 
    }

    /**
     * @test
     * @return void
     */
    public function an_action_can_be_taken()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->setPost();

        $controller = new PlayerActionController($this->actionHandler);
        $response   = $controller->action();

        $this->assertEquals(
            $this->validResponseKeys(),
            array_keys(json_decode($response, true)['body'])
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_adds_a_player_that_calls_the_big_blind_to_the_list_of_table_seats_that_can_continue()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->setPlayerFourCallsPost();

        (new PlayerActionController($this->actionHandler))->action();

        $canContinue = TableSeat::find([
            'table_id' => $this->gameState->getHand()->table()->id,
            'can_continue' => 1
        ]);

        $this->assertCount(1, $canContinue->content);
        $this->assertEquals(1, $this->gamePlay->handTable->seats()->slice(3, 1)->can_continue);
    }

    /**
     * @test
     * @return void
     */
    public function it_removes_a_folded_player_from_the_list_of_seats_that_can_continue()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->givenBigBlindRaisesPreFlopCaller();

        $this->givenPlayerThreeCanContinue();
        $this->setPlayerFourFoldsPost();

        (new PlayerActionController($this->actionHandler))->action();

        $canContinue = TableSeat::find([
            'table_id' => $this->gamePlay->handTable->id,
            'can_continue' => 1
        ]);

        $this->assertCount(1, $canContinue->content);
        $this->assertEquals(0, $this->gamePlay->handTable->seats()->slice(3, 1)->can_continue);
    }

    private function givenBigBlindRaisesPreFlopCaller()
    {
        $this->givenPlayerFourCalls();
        $this->givenPlayerFourCanContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->givenPlayerThreeRaises();
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
}
