<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Action;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\PlayerAction;
use App\Models\Street;
use App\Models\Table;
use App\Models\TableSeat;

class GamePlayHoldEmStreetTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->table = Table::create(['name' => 'Table 2', 'seats' => 3]);
        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 2]));

        $this->player1 = Player::find(['id' => 1]);
        $this->player2 = Player::find(['id' => 2]);
        $this->player3 = Player::find(['id' => 3]);

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

        $this->executeActions();

        $this->gamePlay->play();

        $this->assertCount(2, $this->gamePlay->hand->streets()->content);
        $this->assertCount(3, $this->gamePlay->hand->streets()->slice(1, 1)->cards);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_1_card_to_a_turn()
    {
        $this->gamePlay->start();

        $this->setFlop();

        $this->executeActions();

        $response = $this->gamePlay->play();

        $this->assertCount(3, $this->gamePlay->hand->streets);
        $this->assertCount(1, $this->gamePlay->hand->streets->slice(2, 1)->cards);
        $this->assertTrue($response['players'][2]['action_on']);
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

        $this->executeActions();

        $response = $this->gamePlay->play();

        $this->assertCount(4, $this->gamePlay->hand->streets());
        $this->assertCount(1, $this->gamePlay->hand->streets()->slice(3, 1)->cards);
        $this->assertTrue($response['players'][2]['action_on']);
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

        $this->executeActions();

        $response = $this->gamePlay->play();

        $this->assertNotNull($response['winner']);
    }

    protected function setFlop()
    {
        // Manually set the flop
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
        // Manually set the turn
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
        // Manually set the river
        $river = HandStreet::create([
            'street_id' => Street::find(['name' => 'River'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $river,
            $this->gamePlay->game->streets[3]['community_cards']
        );
    }

    protected function executeActions()
    {
        // Player 1 Calls BB
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(0, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Call'])->id,
                'bet_amount' => 50.0,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);

        // Player 2 Folds
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(1, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Fold'])->id,
                'bet_amount' => null,
                'active' => 0,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 0
            ]);

        // Player 3 Checks
        PlayerAction::find(['id' => $this->gamePlay->hand->actions()->slice(2, 1)->id])
            ->update([
                'action_id' => Action::find(['name' => 'Check'])->id,
                'bet_amount' => null,
                'active' => 1,
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 seconds'))
            ]);

        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(2, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }
}