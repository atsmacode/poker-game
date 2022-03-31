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
    public function a_hand_type_can_be_found_or_created()
    {
        $handType = new HandType(['name' => 'High Card 24', 'ranking' => 16]);

        $this->assertEquals('High Card 24', $handType->name);
        $this->assertEquals(16, $handType->ranking);
    }

}
