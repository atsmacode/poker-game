<?php

namespace Tests\Unit;

use App\Classes\Dealer;
use App\Models\Player;
use App\Models\Table;
use PHPUnit\Framework\TestCase;

class DealerTest extends TestCase
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
        /*
         * Settled for calling setDeck here as the assertion was
         * picking up the same data for some reason.
         */
        $shuffled = $this->dealer->setDeck()->shuffle()->getDeck();

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
    public function it_can_select_a_specific_card()
    {
        $this->assertNotNull($this->dealer->setDeck()->shuffle()->pickCard('Ace', 'Spades')->getCard());
    }

    /**
     * @test
     * @return void
     */
    public function once_a_card_is_picked_it_is_no_longer_in_the_deck()
    {

        $card = $this->dealer->setDeck()->pickCard()->getCard();

        $this->assertNotContains($card, $this->dealer->getDeck()->cards);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_card_to_a_player()
    {
        $player = new Player(['username' => 'Player 1']);

        $this->assertCount(0, $player->wholeCards(true)->content);

        $this->dealer->setDeck()->shuffle()->dealTo($player, 1);

        $this->assertCount(1, $player->wholeCards()->content);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_cards_to_multiple_players_at_a_table()
    {
        $table = new Table([
            'name' => 'Table 1'
        ]);

        /*
         * Manually building the player array here.
         * Might be better to build a hasManyThrough object relation
         * for table > seats > players
         */
        $players = new Player([], true);

        foreach($table->seats(true)->collect()->content as $seat){
            $this->assertCount(0, $seat->player(true)->wholeCards(true)->content);

            $players->content[] = $seat->player(true);
        }

        $this->dealer->setDeck()->shuffle()->dealTo($players, 1);

        foreach($table->seats(true)->collect()->content as $seat){
            $this->assertCount(1, $seat->player(true)->wholeCards(true)->content);
        }

    }

}