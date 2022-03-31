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
        $handType = new HandType('High Card + 16');

        $this->assertEquals('High Card + 16', $handType->name);
    }

}
