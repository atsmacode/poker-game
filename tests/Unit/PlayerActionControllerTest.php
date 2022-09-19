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
        $this->player1  = Player::find(['id' => 1]);
    }

    /**
     * @test
     * @return void
     */
    public function it_returns_valid_response_keys_on_post_request()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_POST['deck']             = $this->gamePlay->dealer->getDeck();

        $this->gamePlay->start();

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
}
