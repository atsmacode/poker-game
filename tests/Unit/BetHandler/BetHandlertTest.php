<?php

namespace Atsmacode\PokerGame\Tests\Unit\BetHandler;

use Atsmacode\PokerGame\BetHandler\BetHandler;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Pot;
use Atsmacode\PokerGame\Models\Stack;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Tests\BaseTest;

class BetHandlerTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->betHandler  = $this->container->get(BetHandler::class);
        $this->tableModel  = $this->container->get(Table::class);
        $this->playerModel = $this->container->get(Player::class);
        $this->stackModel  = $this->container->get(Stack::class);
        $this->potModel    = $this->container->get(Pot::class);
        $this->handModel   = $this->container->get(Hand::class);
    }

    /**
     * @test
     * @return void
     */
    public function a_bet_amount_is_added_to_the_pot_and_subtracted_from_the_player_stack()
    {
        $table  = $this->tableModel->create(['name' => 'Test Table', 'seats' => 3]);
        $player = $this->playerModel->create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $stack = $this->stackModel->create([
            'amount' => 1000,
            'table_id' => $table->id,
            'player_id' => $player->id
        ]);

        $hand = $this->handModel->create(['table_id' => $table->id]);
        $pot  = $this->potModel->create([
            'amount' => 0,
            'hand_id' => $hand->id
        ]);

        $this->assertEquals(1000, $this->stackModel->find(['id' => $stack->id])->amount);

        $this->betHandler->handle($hand, $stack->amount, $player->id, $table->id, 150);

        $this->assertEquals(150, $this->potModel->find(['id' => $pot->id])->amount);
        $this->assertEquals(850, $this->stackModel->find(['id' => $stack->id])->amount);
    }
}
