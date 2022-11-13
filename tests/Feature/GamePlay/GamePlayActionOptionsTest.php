<?php

namespace Tests\Feature\GamePlay;

use App\Classes\GamePlay\GamePlay;
use App\Models\Hand;
use App\Models\TableSeat;
use App\Constants\Action;
use App\Models\Player;
use App\Models\Table;
use Tests\BaseTest;

class GamePlayActionOptionsTest extends BaseTest
{
    use HasGamePlay;

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

        $this->givenPlayerOneRaisesBigBlind();

        $gamePlay = $this->gamePlay->play();

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

        $this->givenPlayerOneFolds();

        $gamePlay = $this->gamePlay->play();

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

        $this->givenPlayerTwoCalls();

        $gamePlay = $this->gamePlay->play();

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

        $this->givenPlayerOneCalls();

        $gamePlay = $this->gamePlay->play();

        $this->assertTrue($gamePlay['players'][1]['action_on']);

        $this->assertContains(ACTION::FOLD, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::CALL, $gamePlay['players'][1]['availableOptions']);
        $this->assertContains(ACTION::RAISE, $gamePlay['players'][1]['availableOptions']);
    }
}
