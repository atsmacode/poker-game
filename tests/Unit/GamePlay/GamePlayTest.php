<?php 

namespace Atsmacode\PokerGame\Tests\Unit\GamePlay;

use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;

class GamePlayTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->tableModel     = $this->container->get(Table::class);
        $this->handModel      = $this->container->get(Hand::class);
        $this->playerModel    = $this->container->get(Player::class);
        $this->tableSeatModel = $this->container->get(TableSeat::class);

        $this->table = $this->tableModel->create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand  = $this->handModel->create(['table_id' => $this->table->id]);

        $this->player1 = $this->playerModel->create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = $this->playerModel->create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        $this->player3 = $this->playerModel->create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->gameState = $this->container->build(GameState::class, [
            'hand' => $this->hand,
        ]);
        
        $this->gamePlay  = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get(PotLimitHoldEm::class),
            'gameState' => $this->gameState,
        ]);
    }

    /** @test */
    public function itCanStartAGame()
    {
        $this->gamePlay->start();

    }
}
