<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Action;
use App\Models\Hand;
use App\Models\Player;
use App\Models\PlayerAction;
use App\Models\Table;
use App\Models\TableSeat;

class GamePlayTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Table 2', 'seats' => 3]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 2]));

        $this->player1 = Player::find(['id' => 1]);
        $this->player2 = Player::find(['id' => 2]);
        $this->player3 = Player::find(['id' => 3]);

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
    public function it_can_start_the_game()
    {
        $response = $this->gamePlay->start();

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

        // the_action_will_be_on_the_player_after_the_big_blind_once_a_hand_is_started
        $this->assertTrue($response['players'][0]['action_on']);
    }

    /**
     * @test
     * @return void
     */
    public function the_big_blind_will_win_the_pot_if_all_other_players_fold_pre_flop()
    {
        $this->gamePlay->start();

        $this->assertCount(1, $this->gamePlay->hand->streets()->content);

        // Player 1 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        $gamePlay = $this->gamePlay->play();

        $this->assertCount(1, $this->gamePlay->hand->streets() ->content);
        $this->assertEquals(1, $gamePlay['players'][2]['can_continue']);
        $this->assertEquals($this->player3->id, $gamePlay['winner']['player']->id);
    }
}