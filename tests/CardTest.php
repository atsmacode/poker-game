<?php

use App\Classes\Dealer;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->deck = (new Dealer())->setDeck()->getDeck();
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_suit()
    {
        $this->assertNotNull(array_shift($this->deck)['suit_id']);
    }

    /**
     * @test
     * @return void
     */
    public function a_card_has_a_rank()
    {
        $this->assertNotNull(array_shift($this->deck)['rank_id']);
    }

}
