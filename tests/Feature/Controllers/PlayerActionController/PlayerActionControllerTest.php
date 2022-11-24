<?php

namespace Tests\Feature\Controllers\PlayerActionController;

use App\Classes\ActionHandler\ActionHandler;
use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;
use Tests\Feature\HasActionPosts;
use Tests\Feature\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table         = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand          = Hand::create(['table_id' => $this->table->id]);
        $this->gamePlay      = new GamePlay($this->hand);

        $this->player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        $this->player3 = Player::create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        $this->player4 = Player::create([
            'name' => 'Player 4',
            'email' => 'player4@rrh.com'
        ]);

        $this->player5 = Player::create([
            'name' => 'Player 5',
            'email' => 'player5@rrh.com'
        ]);

        $this->player6 = Player::create([
            'name' => 'Player 6',
            'email' => 'player6@rrh.com'
        ]);

        $this->seat1 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->seat2 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->seat3 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->seat4 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player4->id
        ]);

        $this->seat5 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player5->id
        ]);

        $this->seat6 = TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player6->id
        ]);

        $this->gameState     = new GameState($this->hand);
        $this->actionHandler = new ActionHandler($this->gameState);
    }

    /**
     * @test
     * @return void
     */
    public function it_returns_expected_response_keys()
    {
        $this->gamePlay->start($this->gameState, null);

        $this->setPost();

        $response = $this->jsonResponse();

        $this->assertEquals(
            $this->validResponseKeys(),
            array_keys($response)
        );
    }

    public function validResponseKeys()
    {
        return [
            'deck',
            'pot',
            'communityCards',
            'players',
            'winner'
        ];
    }
}
