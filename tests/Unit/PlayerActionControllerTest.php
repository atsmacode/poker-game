<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Controllers\PlayerActionController;
use App\Models\Hand;
use App\Models\Player;

class PlayerActionControllerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));
        $this->player   = Player::find(['id' => 4]);
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
            array_keys(json_decode($response, true))
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
        $_SERVER["REQUEST_METHOD"] = "POST";

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player->id,
            'table_seat_id'  => 4,
            'hand_street_id' => 1,
            'action_id'      => 3,
            'bet_amount'     => 50.0,
            'active'         => 1,
        ];

        $_POST['body'] = json_encode(serialize($requestBody));
    }
}
