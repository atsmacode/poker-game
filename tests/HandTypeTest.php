<?php

namespace Tests;

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
        $handType = new HandType('High Card + 7');

        $this->assertEquals('High Card + 7', $handType->name);
    }

}
