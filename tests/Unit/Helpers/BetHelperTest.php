<?php

namespace Atsmacode\PokerGame\Tests\Unit\Helpers;

use Atsmacode\PokerGame\Helpers\BetHelper;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Pot;
use Atsmacode\PokerGame\Models\Stack;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Tests\BaseTest;

class BetHelperTest extends BaseTest
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
        // $table = Table::find(['id' => 1]);
        // $player = Player::find(['id' => 1]);

        // $stack = Stack::create([
        //     'amount' => 1000,
        //     'table_id' => $table->id,
        //     'player_id' => $player->id
        // ]);

        // $hand = Hand::create([
        //     'table_id' => $table->id
        // ]);
        // $pot = Pot::create([
        //     'amount' => 0,
        //     'hand_id' => $hand->id
        // ]);

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
            'amount' => 0,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals(1000, $player->stacks()->search('id', $stack->id)->amount);

        BetHelper::handle($hand, $stack->amount, $player->id, $table->id, 150);

        $this->assertEquals(150, Pot::find(['id' => $pot->id])->amount);
        $this->assertEquals(850, $player->stacks()->search('id', $stack->id)->amount);
    }
}
