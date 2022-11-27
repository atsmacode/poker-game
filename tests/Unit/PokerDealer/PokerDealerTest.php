<?php

namespace Tests\Unit\PokerDealer;

use App\Classes\Dealer\PokerDealer;
use Atsmacode\CardGames\Constants\Card;
use Atsmacode\CardGames\Factory\CardFactory;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\Player;
use App\Models\Street;
use App\Models\Table;
use App\Models\TableSeat;
use Tests\BaseTest;

class PokerDealerTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->dealer = new PokerDealer();
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

        $this->dealer->setDeck()->shuffle()->dealTo($table->seats()->content, 1, $hand);

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

        $card = CardFactory::create(Card::ACE_HEARTS);

        $this->dealer->setDeck()->dealThisStreetCard($card['rank'], $card['suit'], $handStreet);

        $this->assertNotEmpty($handStreet->cards()->collect()->searchMultiple('card_id', $card['id']));
    }
}
