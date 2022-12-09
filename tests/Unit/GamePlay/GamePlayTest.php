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

        var_dump($this->player1->id);

        $this->player2 = $this->playerModel->create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        var_dump($this->player2->id);

        $this->player3 = $this->playerModel->create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        var_dump($this->player3->id);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        var_dump($this->player1->id);

        $this->tableSeatModel->create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        var_dump($this->player2->id);

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
        $response = $this->gamePlay->start();

        //var_dump($response);

        // The small blind was posted
        $this->assertEquals(25, $response['players'][1]['bet_amount']);
        $this->assertEquals('Bet', $response['players'][1]['action_name']);

        // The big blind was posted
        $this->assertEquals(50, $response['players'][2]['bet_amount']);
        $this->assertEquals('Bet', $response['players'][2]['action_name']);

        // The dealer, seat 1, has not acted yet
        $this->assertEquals(null, $response['players'][0]['bet_amount']);
        $this->assertEquals(null, $response['players'][0]['action_id']);

        // Each player in the hand has 2 whole cards
        foreach($response['players'] as $player){
            $this->assertCount(2, $player['whole_cards']);
        }

    }
}
