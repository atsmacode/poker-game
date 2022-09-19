<?php

namespace Tests\Unit;

use App\Controllers\HandController;

class HandControllerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @return void
     */
    public function it_returns_valid_response_keys_on_post_request()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";

        $controller = new HandController();
        $response   = $controller->play();

        $this->assertEquals(
            $this->validResponseKeys(),
            array_keys(json_decode($response, true))
        );
    }
    
    /**
     * @test
     * @return void
     */
    public function it_returns_index_on_get_request()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";

        $controller = new HandController();
        $response   = $controller->play();

        $this->assertEquals(include('resources/index.php'), $response);
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
