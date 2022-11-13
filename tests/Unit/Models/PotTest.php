<?php

namespace Tests\Unit\Models;

use App\Models\Hand;
use App\Models\Pot;
use Tests\BaseTest;

class PotTest extends BaseTest
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

        $this->assertCount(0, $hand->pot()->content);

        $pot = Pot::create([
            'amount' => 75,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals($pot->id, $hand->pot()->id);
    }
}
