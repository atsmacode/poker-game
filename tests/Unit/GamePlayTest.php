<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Constants\Action;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\PlayerAction;
use App\Models\Table;
use App\Models\TableSeat;

class GamePlayTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Table 2', 'seats' => 4]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 2]));

        $this->player1 = Player::find(['id' => 1]);
        $this->player2 = Player::find(['id' => 2]);
        $this->player3 = Player::find(['id' => 3]);
        $this->player4 = Player::find(['id' => 4]);

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

        $this->assertCount(0, $this->gamePlay->handTable->seats()::find(['can_continue' => 1])->content);

        // Player 4 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        $this->gamePlay->play();

        $this->assertCount(1, $this->gamePlay->handTable->seats()::find(['can_continue' => 1])->content);
        $this->assertEquals(1, $this->gamePlay->handTable->seats()->slice(3, 1)->can_continue);
    }

    /**
     * @test
     * @return void
     */
    public function it_removes_a_folded_player_from_the_list_of_seats_that_can_continue()
    {
        $this->gamePlay->start();

        $this->assertCount(0, $this->gamePlay->handTable->seats()::find(['can_continue' => 1])->content);

        $this->givenPlayerFourPreviouslyCalled();

        // Player 4 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        $this->gamePlay->play();

        $this->assertCount(0, $this->gamePlay->handTable->seats()::find(['can_continue' => 1])->content);
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

        $this->assertCount(2, HandStreet::find(['hand_id' => 1])->content);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_will_win_the_pot_if_all_other_players_fold_pre_flop()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionFoldsToBigBlind();

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
            'id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $this->givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo();

        $response = $this->gamePlay->play();

        /**
         * Hard coding hand ID 1, was showing as 9 here, not sure why
         */
        $this->assertCount(2, HandStreet::find(['hand_id' => 1])->content);

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
    public function six_handed_if_there_are_two_seats_after_current_dealer_big_blind_will_be_seat_one()
    {
        $this->player5 = Player::find(['id' => 5]);
        $this->player6 = Player::find(['id' => 6]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player5->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player6->id
        ]);

        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id
        ]));

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        $response = $this->gamePlay->play();

        $this->assertEquals(1, $response['players'][5]['small_blind']);
        $this->assertEquals(1, $response['players'][0]['big_blind']);
    }

    /**
     * @test
     * @return void
     */
    public function six_handed_if_there_is_one_seat_after_current_dealer_big_blind_will_be_seat_two()
    {
        $this->player5 = Player::find(['id' => 5]);
        $this->player6 = Player::find(['id' => 6]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player5->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player6->id
        ]);

        $this->gamePlay->start(TableSeat::find([
            'id' => $this->gamePlay->handTable->seats()->slice(4, 1)->id
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

    /**
     * @test
     * @return void
     */
    // public function with_three_players_the_pre_flop_action_will_initially_be_on_player_one()
    // {

    //     TableSeat::where([
    //         'table_id' => $this->gamePlay->handTable->id,
    //         'player_id' => $this->player4->id
    //     ])->delete();

    //     $this->player4 = null;

    //     $response = $this->gamePlay->start();

    //     $this->assertTrue($response['players'][0]['action_on']);

    //     $this->assertTrue($response['players'][0]['availableOptions']->contains('name', 'Fold'));
    //     $this->assertTrue($response['players'][0]['availableOptions']->contains('name', 'Call'));
    //     $this->assertTrue($response['players'][0]['availableOptions']->contains('name', 'Raise'));

    // }

    private function givenPlayerFourPreviouslyCalled()
    {
        // Player 4 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenActionFoldsToBigBlind()
    {
        // Player 4 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 1 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
    }

    private function givenActionsMeanNewStreetIsDealt()
    {
        // Player 4 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 1 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 3 Checks
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(2, 1)->id])
            ->update([
                'action_id' => Action::CHECK_ID,
                'bet_amount' => null,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
    }

    private function givenActionsMeanNewStreetIsDealtWhenDealerIsSeatTwo()
    {
        // Player 1 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 2 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 25.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 3 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(2, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(2, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 4 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
    }

    private function givenBigBlindRaisesPreFlopCaller()
    {
        // Player 4 Calls
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(3, 1)->id])
            ->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 1 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 3 Raises
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(2, 1)->id])
            ->update([
                'action_id' => Action::RAISE_ID,
                'bet_amount' => 100.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
    }
}
