<?php

namespace Tests\Unit;

use App\Classes\Dealer;
use App\Constants\Card as ConstantsCard;
use App\Models\Card;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\Street;
use App\Models\Table;
use App\Models\TableSeat;

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

        $this->assertNotContains($card, $this->dealer->getDeck());
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_card_to_a_player()
    {
        $player = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->assertCount(0, $player->wholeCards()->content);

        $this->dealer->setDeck()->shuffle()->dealTo($player, 1);

        $this->assertCount(1, $player->wholeCards()->content);
    }

    /**
     * @test
     * @group skip
     * @return void
     */
    public function it_can_deal_cards_to_multiple_players_at_a_table()
    {

        $table = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $hand  = Hand::create(['table_id' => $table->id]);

        $player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        $player3 = Player::create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        TableSeat::create([
            'table_id' => $table->id,
            'player_id' => $player1->id
        ]);

        TableSeat::create([
            'table_id' => $table->id,
            'player_id' => $player2->id
        ]);

        TableSeat::create([
            'table_id' => $table->id,
            'player_id' => $player3->id
        ]);

        foreach($table->players()->collect()->content as $player){
            $this->assertCount(0, $player->wholeCards()->content);
        }

        $this->dealer->setDeck()->shuffle()->dealTo($table->players(), 1, $hand);

        foreach($table->players()->collect()->content as $player){
            $this->assertCount(1, $player->wholeCards()->content);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_street_card()
    {

        $handStreet = HandStreet::create([
            'street_id' => Street::find(['name' => 'Flop'])->id,
            'hand_id' => Hand::create(['table_id' => 1])->id
        ]);

        $this->dealer->setDeck()->dealStreetCards(
            $handStreet,
            1
        );

        $this->assertCount(1, $handStreet->cards()->content);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_specific_street_card()
    {
        $handStreet = HandStreet::create([
            'street_id' => Street::find(['name' => 'Flop'])->id,
            'hand_id' => Hand::create(['table_id' => 1])->id
        ]);

        $card = new Card(ConstantsCard::ACE_HEARTS);

        $this->dealer->setDeck()->dealThisStreetCard($card->rank, $card->suit, $handStreet);

        $this->assertNotEmpty($handStreet->cards()->collect()->searchMultiple('card_id', $card->id));
    }
}
