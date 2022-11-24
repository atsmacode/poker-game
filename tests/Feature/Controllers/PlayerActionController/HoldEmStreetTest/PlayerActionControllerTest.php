<?php

namespace Tests\Feature\Controllers\PlayerActionController\HoldEmStreetTest;

use App\Classes\ActionHandler\ActionHandler;
use App\Classes\GamePlay\GamePlay;
use App\Classes\GameState\GameState;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\Street;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;
use Tests\Feature\HasActionPosts;
use Tests\Feature\HasGamePlay;
use Tests\Feature\HasStreets;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;
    use HasStreets;

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

        TableSeat::create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->gameState     = new GameState($this->hand);
        $this->actionHandler = new ActionHandler($this->gameState);
    }

     /**
     * @test
     * @return void
     */
    public function it_can_deal_3_cards_to_a_flop()
    {
        $this->gamePlay->start($this->gameState, null);

        $this->executeActionsToContinue();

        $this->jsonResponse();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(3, HandStreet::getStreetCards($this->gameState->handId(), 2));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_1_card_to_a_turn()
    {
        $this->gamePlay->start($this->gameState, null);

        $this->setFlop();

        $this->executeActionsToContinue();

        $this->jsonResponse();

        $this->assertCount(3, HandStreet::find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(1, HandStreet::getStreetCards($this->gameState->handId(), 3));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_1_card_to_a_river()
    {
        $this->gamePlay->start($this->gameState, null);

        $this->setFlop();

        $this->setTurn();

        $this->executeActionsToContinue();

        $this->jsonResponse();

        $this->assertCount(4, HandStreet::find(['hand_id' => $this->gameState->handId()])->content);
        $this->assertCount(1, HandStreet::getStreetCards($this->gameState->handId(), 4));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_reach_showdown_when_all_active_players_can_continue_on_the_river()
    {
        $this->gamePlay->start($this->gameState, null);

        $this->setFlop();

        $this->setTurn();

        $this->setRiver();

        $this->executeActionsToContinue();

        $response = $this->jsonResponse();

        $this->assertNotNull($response['winner']);
    }

    protected function executeActionsToContinue()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->setPlayerThreeChecksPost();
    }
}
