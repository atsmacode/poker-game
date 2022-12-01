<?php

namespace Atsmacode\PokerGame\Tests\Unit\PokerDealer;

use Atsmacode\PokerGame\Dealer\PokerDealer;
use Atsmacode\CardGames\Constants\Card;
use Atsmacode\CardGames\Factory\CardFactory;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Street;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;

class PokerDealerTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->dealer      = new PokerDealer();
        $this->streetModel = $this->container->get(Street::class);
    }

    /**
     * @test
     * @group skip
     * @return void
     */
    public function it_can_deal_cards_to_multiple_players_at_a_table()
    {

        $table              = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $hand               = Hand::create(['table_id' => $table->id]);
        $this->tableSeatDal = $this->container->get(TableSeat::class);

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

        $this->tableSeatDal->create([
            'table_id' => $table->id,
            'player_id' => $player1->id
        ]);

        $this->tableSeatDal->create([
            'table_id' => $table->id,
            'player_id' => $player2->id
        ]);

        $this->tableSeatDal->create([
            'table_id' => $table->id,
            'player_id' => $player3->id
        ]);

        $tableSeats = $this->tableSeatDal->find(['table_id' => $table->id]);

        foreach ($tableSeats->content as $tableSeat) {
            $this->assertCount(0, Player::getWholeCards($hand->id, $tableSeat['player_id']));
        }

        $this->dealer->setDeck()->shuffle()->dealTo($tableSeats->content, 1, $hand);

        foreach ($tableSeats->content as $tableSeat) {
            $this->assertCount(1, Player::getWholeCards($hand->id, $tableSeat['player_id']));
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_street_card()
    {

        $handStreet = HandStreet::create([
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->id,
            'hand_id'   => Hand::create(['table_id' => 1])->id
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
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->id,
            'hand_id'   => Hand::create(['table_id' => 1])->id
        ]);

        $card = CardFactory::create(Card::ACE_HEARTS);

        $this->dealer->setDeck()->dealThisStreetCard($card['rank'], $card['suit'], $handStreet);

        $this->assertNotEmpty($handStreet->cards()->collect()->searchMultiple('card_id', $card['id']));
    }
}
