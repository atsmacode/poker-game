<?php

namespace Tests\Unit;

use App\Models\HandType;
use App\Models\TableSeat;
use PHPUnit\Framework\TestCase;

class TableSeatTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_table_seat_can_be_updated()
    {
        $tableSeat = new TableSeat(['id' => 1]);

        $this->assertEquals(0, $tableSeat->can_continue);

        $tableSeat->update(['can_continue' => 1]);

        $this->assertEquals(1, $tableSeat->can_continue);

    }

}
