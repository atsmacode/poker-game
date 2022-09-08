<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Hand;
use App\Models\PlayerAction;

class PlayerActionTest extends BaseTest
{
    public function setup(): void
    {
        parent::setUp();
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_select_first_active_player_after_dealer()
    {
        $this->gamePlay->start();

        $playerAction = PlayerAction::playerAfterDealer(
            $this->gamePlay->hand->id,
            1
        );

        $this->assertEquals(2, $playerAction['id']);
    }
}