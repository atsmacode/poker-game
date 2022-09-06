<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Action;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\TableSeat;

class ActionOptionsTest extends BaseTest
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));

        $this->fold  = $this->gamePlay->fold;
        $this->check = $this->gamePlay->check;
        $this->call  = $this->gamePlay->call;
        $this->bet   = $this->gamePlay->bet;
        $this->raise = $this->gamePlay->raise;
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_bet_can_fold_call_or_raise()
    {
        $gamePlay = $this->gamePlay->start();

        $this->assertTrue($gamePlay['players'][3]['action_on']);

        $this->assertContains($this->fold, $gamePlay['players'][3]['availableOptions']);
        $this->assertContains($this->call, $gamePlay['players'][3]['availableOptions']);
        $this->assertContains($this->raise, $gamePlay['players'][3]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_raise_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        // Player 1 Raises BB
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Raise'])->id,
                'bet_amount' => 100.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        $gamePlay = $this->gamePlay->play();

        // Action On BB
        $this->assertTrue($gamePlay['players'][2]['action_on']);

        $this->assertContains($this->fold, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains($this->call, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains($this->raise, $gamePlay['players'][2]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_folded_player_has_no_options()
    {
        $this->gamePlay->start();

        // Player 1 Raises BB
        PlayerAction::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Raise'])->id,
                'bet_amount' => 100.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        $gamePlay = $this->gamePlay->play();

        // Action On BB
        $this->assertTrue($gamePlay['players'][2]['action_on']);

        $this->assertEmpty($gamePlay['players'][1]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_facing_a_call_can_fold_check_or_raise()
    {
        $this->gamePlay->start();

        // Player 1 Calls BB
        PlayerAction::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Call'])->id,
                'bet_amount' => 50.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        $gamePlay = $this->gamePlay->play();

        // Action On BB
        $this->assertTrue($gamePlay['players'][2]['action_on']);

        $this->assertContains($this->fold, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains($this->check, $gamePlay['players'][2]['availableOptions']);
        $this->assertContains($this->raise, $gamePlay['players'][2]['availableOptions']);
    }

    /**
     * @test
     * @return void
     */
    public function a_player_facing_a_call_can_fold_call_or_raise()
    {
        $this->gamePlay->start();

        // Player 1 Calls BB
        PlayerAction::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Call'])->id,
                'bet_amount' => 50.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        $gamePlay = $this->gamePlay->play();

        // Action On SB
        $this->assertTrue($gamePlay['players'][1]['action_on']);

        $this->assertContains($this->fold, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains($this->call, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains($this->raise, $gamePlay['players'][1]['availableOptions']);
    }
}
