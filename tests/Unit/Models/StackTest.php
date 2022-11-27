<?php

namespace Atsmacode\PokerGame\Tests\Unit\Models;

use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Stack;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Tests\BaseTest;

class StackTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function a_player_can_have_a_stack()
    {
        $table = Table::find(['id' => 1]);

        $player = Player::find(['id' => 1]);

        $stack = Stack::create([
            'amount' => 1000,
            'table_id' => $table->id,
            'player_id' => $player->id
        ]);

        $this->assertNotEmpty($player->stacks()->collect()->searchMultiple('id', $stack->id));
    }
}
