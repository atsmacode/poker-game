<?php

namespace Tests\Unit;

use App\Helpers\PotHelper;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;
use App\Models\Stack;
use App\Models\Table;
use PHPUnit\Framework\TestCase;

class PotHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_pot_can_be_initiated()
    {
        $table = Table::find(['id' => 1]);
        $hand = Hand::create([
            'table_id' => $table->id
        ]);

        $this->assertNotInstanceOf(Pot::class, PotHelper::initiatePot($hand));
    }

    /**
     * @test
     * @return void
     */
    public function a_pot_can_be_awarded_to_a_player()
    {

        $table = Table::find(['id' => 1]);
        $player = Player::find(['id' => 1]);
        $stack = Stack::create([
            'amount' => 1000,
            'table_id' => $table->id,
            'player_id' => $player->id
        ]);

        $hand = Hand::create([
            'table_id' => $table->id
        ]);
        $pot = Pot::create([
            'amount' => 75,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals(1000, $player->stacks()->search('id', $stack->id)->amount);

        PotHelper::awardPot($pot, $player);

        $this->assertEquals(1075, $player->stacks()->search('id', $stack->id)->amount);
    }
}
