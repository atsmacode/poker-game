<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\HoldEmStreetTest;

use Atsmacode\PokerGame\Classes\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\Feature\HasActionPosts;
use Atsmacode\PokerGame\Tests\Feature\HasGamePlay;
use Atsmacode\PokerGame\Tests\Feature\HasStreets;

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

        $this->gameState = new GameState(new GameData(), $this->hand);
        $this->gamePlay  = new GamePlay(
            $this->gameState,
            new Start(),
            new NewStreet(),
            new Showdown(),
            new PlayerHandler()
        );

        $this->actionHandler = new ActionHandler($this->gameState);
    }

     /**
     * @test
     * @return void
     */
    public function it_can_deal_3_cards_to_a_flop()
    {
        $this->gamePlay->start();

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
        $this->gamePlay->start();

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
        $this->gamePlay->start();

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
        $this->gamePlay->start();

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
