<?php

use App\Classes\Dealer;

class DealerTest extends BaseTest
{

    public function setUp(): void
    {
        parent::setUp();

        $this->dealer = new Dealer();
    }

    /**
     * @test
     * @return void
     */
    public function it_can_shuffle_the_deck()
    {
        $unshuffled = $this->dealer->setDeck()->getDeck();
        $shuffled = $this->dealer->shuffle()->getDeck();

        $this->assertNotSame($unshuffled, $shuffled);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_select_a_random_card()
    {
        $this->assertNotNull($this->dealer->setDeck()->shuffle()->pickCard()->getCard());
    }

    /**
     * @test
     * @return void
     */
    public function once_a_card_is_picked_it_is_no_longer_in_the_deck()
    {

        $card = $this->dealer->setDeck()->pickCard()->getCard();

        $this->assertNotContains($card, $this->dealer->getDeck());
    }

}