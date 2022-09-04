<?php

namespace Tests\Unit;

use App\Helpers\BetHelper;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;
use App\Models\Stack;
use App\Models\Table;
use PHPUnit\Framework\TestCase;

class BetHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_bet_amount_is_added_to_the_pot_and_subtracted_from_the_player_stack()
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
            'amount' => 0,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals(1000, $player->stacks()->search('id', $stack->id)->amount);

        BetHelper::handle($hand, $player, 150);

        $this->assertEquals(150, Pot::find(['id' => $pot->id])->amount);
        $this->assertEquals(850, $player->stacks()->search('id', $stack->id)->amount);
    }
}
