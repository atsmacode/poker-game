<?php

namespace Atsmacode\PokerGame\Tests\Unit\PokerDealer;

use Atsmacode\CardGames\Constants\Card;
use Atsmacode\CardGames\Factory\CardFactory;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class PokerDealerTest extends BaseTest
{
    use HasGamePlay;

    public function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();
    }

    /**
     * @test
     * @group skip
     * @return void
     */
    public function it_can_deal_cards_to_multiple_players_at_a_table()
    {

        foreach($this->table->getSeats() as $tableSeat){
            $this->assertCount(0, $this->playerModel->getWholeCards($this->hand->id, $tableSeat['player_id']));
        }

        $this->pokerDealer->setDeck()->shuffle()->dealTo($this->table->getSeats(), 1, $this->hand->id);

        foreach($this->table->getSeats() as $tableSeat){
            $this->assertCount(1, $this->playerModel->getWholeCards($this->hand->id, $tableSeat['player_id']));
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_street_card()
    {

        $handStreet = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->id,
            'hand_id'   => $this->handModel->create(['table_id' => $this->table->id])->id
        ]);

        $this->pokerDealer->setDeck()->dealStreetCards(
            $handStreet,
            1
        );

        $this->assertCount(1, $handStreet->cards());
    }

    /**
     * @test
     * @return void
     */
    public function it_can_deal_a_specific_street_card()
    {
        $handStreet = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->id,
            'hand_id'   => $this->handModel->create(['table_id' => $this->table->id])->id
        ]);

        $card = CardFactory::create(Card::ACE_HEARTS);

        $this->pokerDealer->setDeck()->dealThisStreetCard($card['rank'], $card['suit'], $handStreet);

        $this->assertContains($card['id'], array_column($handStreet->cards(), 'card_id'));
    }
}
