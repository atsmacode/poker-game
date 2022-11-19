<?php

namespace Tests\Feature\GamePlay;

use App\Classes\GamePlay\GamePlay;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\Street;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class GamePlayHoldEmStreetTest extends BaseTest
{
    use HasGamePlay;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => $this->table->id]));

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
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id' => $this->gamePlay->handTable->id,
            'player_id' => $this->player3->id
        ]);
    }

     /**
     * @test
     * @return void
     */
    public function it_can_deal_3_cards_to_a_flop()
    {
        $this->gamePlay->start();

        $this->executeActionsToContinue();

        $this->gamePlay->play();

        $this->assertCount(2, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);
        $this->assertCount(3, HandStreet::getStreetCards($this->gamePlay->handId, 2));
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

        $this->gamePlay->play();

        $this->assertCount(3, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);
        $this->assertCount(1, HandStreet::getStreetCards($this->gamePlay->handId, 3));
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

        $this->gamePlay->play();

        $this->assertCount(4, HandStreet::find(['hand_id' => $this->gamePlay->handId])->content);
        $this->assertCount(1, HandStreet::getStreetCards($this->gamePlay->handId, 4));
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

        $response = $this->gamePlay->play();

        $this->assertNotNull($response['winner']);
    }

    protected function setFlop()
    {
        $flop = HandStreet::create([
            'street_id' => Street::find(['name' => 'Flop'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $flop,
            $this->gamePlay->game->streets[1]['community_cards']
        );
    }

    protected function setTurn()
    {
        $turn = HandStreet::create([
            'street_id' => Street::find(['name' => 'Turn'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $turn,
            $this->gamePlay->game->streets[2]['community_cards']
        );
    }

    protected function setRiver()
    {
        $river = HandStreet::create([
            'street_id' => Street::find(['name' => 'River'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $river,
            $this->gamePlay->game->streets[3]['community_cards']
        );
    }
}
