<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController;

use Atsmacode\PokerGame\Classes\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\Feature\HasActionPosts;
use Atsmacode\PokerGame\Tests\Feature\HasGamePlay;

class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;

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
    public function it_returns_expected_response_keys()
    {
        $this->gamePlay->start();

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
