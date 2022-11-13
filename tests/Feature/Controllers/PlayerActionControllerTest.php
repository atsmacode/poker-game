<?php

namespace Tests\Feature\Controllers;

use App\Classes\GamePlay\GamePlay;
use App\Constants\Action;
use App\Controllers\PlayerActionController;
use App\Models\Hand;
use App\Models\Player;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class PlayerActionControllerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->table    = Table::create(['name' => 'Test Table', 'seats' => 3]);
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
    public function an_action_can_be_taken()
    {
        $this->gamePlay->start();

        $this->setPost();

        $controller = new PlayerActionController();
        $response   = $controller->action();

        $this->assertEquals(
            $this->validResponseKeys(),
            array_keys(json_decode($response, true)['body'])
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

    private function setPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player1->id,
            'table_seat_id'  => $this->gamePlay->handTable->seats()->slice(0, 1)->id,
            'hand_street_id' => $this->gamePlay->hand->streets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50.0,
            'active'         => 1,
        ];

        $_POST['body'] = serialize($requestBody);
    }
}
