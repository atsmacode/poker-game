<?php

namespace Tests\Unit\Helpers;

use App\Helpers\PotHelper;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;
use App\Models\Stack;
use App\Models\Table;
use Tests\BaseTest;

class PotHelperTest extends BaseTest
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
        $table = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $hand  = Hand::create(['table_id' => $table->id]);

        $this->assertNotInstanceOf(Pot::class, PotHelper::initiatePot($hand));
    }

    /**
     * @test
     * @return void
     */
    public function a_pot_can_be_awarded_to_a_player()
    {

        $table  = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $player = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);
        $stack  = Stack::create([
            'amount' => 1000,
            'table_id' => $table->id,
            'player_id' => $player->id
        ]);
        $hand   = Hand::create(['table_id' => $table->id]);
        $pot    = Pot::create([
            'amount' => 75,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals(1000, $player->stacks()->search('id', $stack->id)->amount);

        PotHelper::awardPot($stack->amount, $pot->amount, $player->id, $table->id);

        $this->assertEquals(1075, $player->stacks()->search('id', $stack->id)->amount);
    }
}
