<?php

namespace Atsmacode\PokerGame\Tests\Unit;

use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class TableSeatTest extends BaseTest
{
    use HasGamePlay;

    public function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();
    }

    /**
     * @test
     * @return void
     */
    public function a_table_seat_can_be_updated()
    {
        $tableSeat = $this->tableSeatModel->find(['id' => $this->gameState->getSeats()[0]['id']]);

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
        $this->gamePlay->start($this->tableSeatModel->find([
            'id' => $this->gameState->getSeats()[0]['id']
        ]));

        $tableSeat = $this->tableSeatModel->playerAfterDealer(
            $this->gameState->handId(),
            $this->gameState->getSeats()[0]['id']
        );

        $this->assertEquals($this->gameState->getSeats()[1]['id'], $tableSeat->id);
    }
}
