<?php

namespace Tests\Unit;

use App\Models\HandType;
use PHPUnit\Framework\TestCase;

class HandTypeTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_hand_type_can_be_created()
    {
        $handType = HandType::create(['name' => 'High Card 24', 'ranking' => 16]);

        $this->assertEquals('High Card 24', $handType->name);
        $this->assertEquals(16, $handType->ranking);
    }

    /**
     * @test
     * @return void
     */
    public function a_hand_type_can_be_found()
    {
        $handType = HandType::find(['name' => 'High Card', 'ranking' => 10]);

        $this->assertEquals('High Card', $handType->name);
        $this->assertEquals(10, $handType->ranking);
    }

}
