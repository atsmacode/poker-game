<?php

namespace Tests\Feature\GamePlay;

use App\Classes\GamePlay\GamePlay;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class GamePlayFourHandedTest extends BaseTest
{
    use HasGamePlay;

    public function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 4]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => $this->table ->id]));

        $this->player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player3@rrh.com'
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
    public function it_can_start_the_game()
    {
        $this->gamePlay->start();

        // The small blind was posted
        $this->assertEquals(25.0, $this->gamePlay->hand->actions()->slice(1, 1)->bet_amount);
        $this->assertEquals('Bet', $this->gamePlay->hand->actions()->slice(1, 1)->action()->name);

        // The big blind was posted
        $this->assertEquals(50.0, $this->gamePlay->hand->actions()->slice(2, 1)->bet_amount);
        $this->assertEquals('Bet', $this->gamePlay->hand->actions()->slice(2, 1)->action()->name);

        // The dealer, seat 1, has not acted yet
        $this->assertEquals(null, $this->gamePlay->hand->actions()->slice(0, 1)->bet_amount);
        $this->assertEquals(null, $this->gamePlay->hand->actions()->slice(0, 1)->action_id);

        // Each player in the hand has 2 whole cards
        foreach($this->gamePlay->handTable->players()->collect()->content as $player){
            $this->assertCount(2, $player->wholeCards()->searchMultiple('hand_id', $this->gamePlay->hand->id));
        }
    }

    /**
     * @test
     * @return void
     */
    public function the_pre_flop_action_will_initially_be_on_the_player_after_big_blind()
    {
        $response = $this->gamePlay->start();

        $this->assertTrue($response['players'][3]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function it_adds_a_player_that_calls_the_big_blind_to_the_list_of_table_seats_that_can_continue()
    {
        $this->gamePlay->start();

        $this->givenPlayerFourCalls();

        $this->gamePlay->play();

        $canContinue = TableSeat::find([
            'table_id' => $this->gamePlay->handTable->id,
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
        $this->gamePlay->start();

        $this->givenBigBlindRaisesPreFlopCaller();

        $this->givenPlayerThreeCanContinue();
        $this->givenPlayerFourFolds();

        $this->gamePlay->play();

        $canContinue = TableSeat::find([
            'table_id' => $this->gamePlay->handTable->id,
            'can_continue' => 1
        ]);

        $this->assertCount(1, $canContinue->content);
        $this->assertEquals(0, $this->gamePlay->handTable->seats()->slice(3, 1)->can_continue);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_new_street()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealt();

        $this->gamePlay->play();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_will_win_the_pot_if_all_other_players_fold_pre_flop()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenPlayerFourFolds();
        $this->givenPlayerFourCanNotContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->givenPlayerTwoFolds();

        $gamePlay = $this->gamePlay->play();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);
        $this->assertEquals(1, $gamePlay['players'][2]['can_continue']);
        $this->assertEquals($this->player3->id, $gamePlay['winner']['player']->id);
    }

    /**
     * @test
     * @return void
     */
    public function the_pre_flop_action_will_be_back_on_the_big_blind_caller_if_the_big_blind_raises()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets() ->content);

        $this->givenBigBlindRaisesPreFlopCaller();

        $response = $this->gamePlay->play();

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
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo();

        $response = $this->gamePlay->play();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);

        $this->assertTrue($response['players'][2]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function four_handed_if_there_is_one_seat_after_current_dealer_big_blind_will_be_seat_two()
    {
        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(2, 1)->id
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $response = $this->gamePlay->play();

        $this->assertEquals(1, $response['players'][0]['small_blind']);
        $this->assertEquals(1, $response['players'][1]['big_blind']);
    }

    /**
     * @test
     * @return void
     */
    public function if_the_dealer_is_the_first_active_seat_on_a_new_street_the_first_active_seat_after_them_will_be_first_to_act()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealt();

        $response = $this->gamePlay->play();

        $this->assertTrue($response['players'][2]['action_on']);
    }

    private function givenActionsMeanNewStreetIsDealt()
    {
        $this->givenPlayerFourCalls();
        $this->givenPlayerFourCanContinue();

        $this->givenPlayerOneFolds();
        $this->givenPlayerOneCanNotContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->givenPlayerThreeChecks();
    }

    private function givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoCalls();
        $this->givenPlayerTwoCanContinue();

        $this->givenPlayerThreeCallsSmallBlind();
        $this->givenPlayerThreeCanContinue();

        $this->givenPlayerFourChecks();
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
}
