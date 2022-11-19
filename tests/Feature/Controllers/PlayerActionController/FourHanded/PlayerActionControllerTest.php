<?php

namespace Tests\Feature\Controllers\PlayerActionController\FourHanded;

use App\Classes\ActionHandler\ActionHandler;
use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Models\Hand;
use App\Models\HandStreet;
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
    public function it_adds_a_player_that_calls_the_big_blind_to_the_list_of_table_seats_that_can_continue()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->setPlayerFourCallsPost();

        $response = $this->jsonResponse();

        $this->assertEquals(1, $response['players'][3]['can_continue']);
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

        $response = $this->jsonResponse();

        $this->assertEquals(0, $response['players'][3]['can_continue']);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_new_street()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealt();

        $this->jsonResponse();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_will_win_the_pot_if_all_other_players_fold_pre_flop()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenPlayerFourFolds();
        $this->givenPlayerFourCanNotContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->setPlayerTwoFoldsPost();

        $response = $this->jsonResponse();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);
        $this->assertEquals(1, $response['players'][2]['can_continue']);
        $this->assertEquals($this->player3->id, $response['winner']['player']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function the_pre_flop_action_will_be_back_on_the_big_blind_caller_if_the_big_blind_raises()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets() ->content);

        $this->givenBigBlindRaisesPreFlopCaller();

        $response = $this->jsonResponse();

        // We are still on the pre-flop action
        $this->assertCount(1, $this->gamePlay->hand->streets() ->content);

        $this->assertTrue($response['players'][3]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function if_the_dealer_is_seat_two_and_the_first_active_seat_on_a_new_street_the_first_active_seat_after_them_will_be_first_to_act()
    {
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id
        ]), $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo();

        $response = $this->jsonResponse();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);

        $this->assertTrue($response['players'][2]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function if_there_is_one_seat_after_current_dealer_big_blind_will_be_seat_two()
    {
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(2, 1)->id
        ]), $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $response = $this->jsonResponse();

        $this->assertEquals(1, $response['players'][0]['small_blind']);
        $this->assertEquals(1, $response['players'][1]['big_blind']);
    }

    /**
     * @test
     * @return void
     */
    public function if_the_dealer_is_the_first_active_seat_on_a_new_street_the_first_active_seat_after_them_will_be_first_to_act()
    {
        $this->gamePlay->start(null, $this->gameState);

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealt();

        $response = $this->jsonResponse();

        $this->assertTrue($response['players'][2]['action_on']);
    }

    private function givenBigBlindRaisesPreFlopCaller()
    {
        $this->givenPlayerFourCalls();
        $this->givenPlayerFourCanContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->setPlayerThreeRaisesPost();
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

    private function givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoCalls();
        $this->givenPlayerTwoCanContinue();

        $this->givenPlayerThreeCallsSmallBlind();
        $this->givenPlayerThreeCanContinue();

        $this->setPlayerFourChecksPost();
    }
}