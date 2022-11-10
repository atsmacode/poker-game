<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\TableSeat;
use App\Constants\Action;
use App\Models\Player;
use App\Models\PlayerActionLog;
use App\Models\Table;

class ActionOptionsTest extends BaseTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => $this->table->id]));

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
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_raise_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        // Player 1 Raises BB
        $lastToAct = PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id]);

        $lastToAct->update([
                'action_id' => Action::RAISE_ID,
                'bet_amount' => 100.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        PlayerActionLog::create([
            'player_status_id' => $lastToAct->id,
            'bet_amount'       => $lastToAct->bet_amount,
            'big_blind'        => $lastToAct->big_blind,
            'small_blind'      => $lastToAct->small_blind,
            'player_id'        => $lastToAct->player_id,
            'action_id'        => $lastToAct->action_id,
            'hand_id'          => $this->gamePlay->handId,
            'hand_street_id'   => $lastToAct->hand_street_id,
            'table_seat_id'    => $lastToAct->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = $this->gamePlay->play();

        // Action On SB
        $this->assertTrue($gamePlay['players'][1]['action_on']);

        $this->assertContains(ACTION::FOLD, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::CALL, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::RAISE, $gamePlay['players'][1]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_folded_player_has_no_options()
    {
        $this->gamePlay->start();

        $lastToAct = PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id]);

        $lastToAct->update([
                'action_id' => Action::FOLD_ID,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        PlayerActionLog::create([
            'player_status_id' => $lastToAct->id,
            'bet_amount'       => $lastToAct->bet_amount,
            'big_blind'        => $lastToAct->big_blind,
            'small_blind'      => $lastToAct->small_blind,
            'player_id'        => $lastToAct->player_id,
            'action_id'        => $lastToAct->action_id,
            'hand_id'          => $this->gamePlay->handId,
            'hand_street_id'   => $lastToAct->hand_street_id,
            'table_seat_id'    => $lastToAct->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = $this->gamePlay->play();

        // Action On BB
        $this->assertTrue($gamePlay['players'][1]['action_on']);
        $this->assertEmpty($gamePlay['players'][0]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_facing_a_call_can_fold_check_or_raise()
    {
        $this->gamePlay->start();

        // Player 2 SB Calls
        $lastToAct = PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id]);

        $lastToAct->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 25.00,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        PlayerActionLog::create([
            'player_status_id' => $lastToAct->id,
            'bet_amount'       => $lastToAct->bet_amount,
            'big_blind'        => $lastToAct->big_blind,
            'small_blind'      => $lastToAct->small_blind,
            'player_id'        => $lastToAct->player_id,
            'action_id'        => $lastToAct->action_id,
            'hand_id'          => $this->gamePlay->handId,
            'hand_street_id'   => $lastToAct->hand_street_id,
            'table_seat_id'    => $lastToAct->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = $this->gamePlay->play();

        // Action On BB
        $this->assertTrue($gamePlay['players'][2]['action_on']);

        $this->assertContains(ACTION::FOLD, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains(ACTION::CHECK, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains(ACTION::RAISE, $gamePlay['players'][2]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_call_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        // Player 1 Calls BB
        $lastToAct = PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id]);

        $lastToAct->update([
                'action_id' => Action::CALL_ID,
                'bet_amount' => 50.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        PlayerActionLog::create([
            'player_status_id' => $lastToAct->id,
            'bet_amount'       => $lastToAct->bet_amount,
            'big_blind'        => $lastToAct->big_blind,
            'small_blind'      => $lastToAct->small_blind,
            'player_id'        => $lastToAct->player_id,
            'action_id'        => $lastToAct->action_id,
            'hand_id'          => $this->gamePlay->handId,
            'hand_street_id'   => $lastToAct->hand_street_id,
            'table_seat_id'    => $lastToAct->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = $this->gamePlay->play();

        // Action On SB
        $this->assertTrue($gamePlay['players'][1]['action_on']);

        $this->assertContains(ACTION::FOLD, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::CALL, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::RAISE, $gamePlay['players'][1]['availableOptions']);
    }
}
