<?php

namespace Tests\Unit;

use App\Models\Card;

class CardTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();
        
        $this->card = new Card([
            'rank' => 'Ace',
            'suit' => 'Spades'
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_suit()
    {
        $this->assertEquals('Spades', $this->card->suit);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_rank()
    {
        $this->assertEquals('Ace', $this->card->rank);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_ranking()
    {
        $this->assertEquals(1, $this->card->ranking);
    }

}
