<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Hand;
use App\Models\TableSeat;

class TableSeatTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));
    }

    /**
     * @test
     * @return void
     */
    public function a_table_seat_can_be_updated()
    {
        $tableSeat = TableSeat::find(['id' => 1]);

        $this->assertEquals(0, $tableSeat->can_continue);

        $tableSeat->update(['can_continue' => 1]);

        $this->assertEquals(1, $tableSeat->can_continue);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_select_first_active_player_after_dealer()
    {
        $this->gamePlay->start();

        $tableSeat = TableSeat::playerAfterDealer(
            $this->gamePlay->hand->id,
            1
        );

        $this->assertEquals(2, $tableSeat->id);
    }
}
