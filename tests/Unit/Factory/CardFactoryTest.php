<?php

namespace Tests\Unit\Factory;

use App\Constants\Card;
use App\Factory\CardFactory;
use Tests\BaseTest;

class CardFactoryTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();
        
        $this->card = CardFactory::create(Card::ACE_SPADES);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_suit()
    {
        $this->assertEquals('Spades', $this->card['suit']);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_rank()
    {
        $this->assertEquals('Ace', $this->card['rank']);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_ranking()
    {
        $this->assertEquals(1, $this->card['ranking']);
    }

}
