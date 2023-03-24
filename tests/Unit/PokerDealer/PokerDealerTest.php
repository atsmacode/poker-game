<?php

namespace Atsmacode\PokerGame\Tests\Unit\PokerDealer;

use Atsmacode\CardGames\Constants\Card;
use Atsmacode\CardGames\Factory\CardFactory;
use Atsmacode\PokerGame\Models\Deck;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class PokerDealerTest extends BaseTest
{
    use HasGamePlay;

    private Deck $deckModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded()
            ->setHand()
            ->setGamePlay();

        $this->deckModel = $this->container->build(Deck::class);
    }

    /**
     * @test
     * @return void
     */
    public function itCanDealCardsToMultiplePlayersAtATable()
    {
        foreach($this->table->getSeats() as $tableSeat){
            $this->assertCount(0, $this->playerModel->getWholeCards($this->hand->getId(), $tableSeat['player_id']));
        }

        $this->pokerDealer->setDeck()->shuffle()->dealTo($this->table->getSeats(), 1, $this->hand->getId());

        foreach($this->table->getSeats() as $tableSeat){
            $this->assertCount(1, $this->playerModel->getWholeCards($this->hand->getId(), $tableSeat['player_id']));
        }
    }

    /**
     * @test
     * @return void
     */
    public function itCanDealAStreetCard()
    {
        $handStreet = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->getId(),
            'hand_id'   => $this->handModel->create(['table_id' => $this->table->getId()])->getId()
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
    public function itCanDealASpecificStreetCard()
    {
        $handStreet = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => 'Flop'])->getId(),
            'hand_id'   => $this->handModel->create(['table_id' => $this->table->getId()])->getId()
        ]);

        $card = CardFactory::create(Card::ACE_HEARTS);

        $this->pokerDealer->setDeck()->dealThisStreetCard($card['rank'], $card['suit'], $handStreet);

        $this->assertContains($card['id'], array_column($handStreet->cards(), 'card_id'));
    }

    /**
     * @test
     * @return void
     */
    public function itCanSaveADeck()
    {
        $this->pokerDealer->setDeck()->saveDeck($this->hand->getId());

        $dealerDeck = $this->pokerDealer->getDeck();
        $savedDeck  = $this->deckModel->find(['hand_id' => $this->hand->getId()]);

        $this->assertSame($dealerDeck, $savedDeck->getDeck());
    }
}
