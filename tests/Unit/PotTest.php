<?php

namespace Tests\Unit;

use App\Models\Hand;
use App\Models\Pot;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class PotTest extends FrameworkTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_hand_can_have_a_pot()
    {
        $hand = Hand::create(['table_id' => 1]);

        $this->assertNull($hand->pot());

        $pot = Pot::create([
            'amount' => 75,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals($pot->id, $hand->pot()->id);
    }
}
