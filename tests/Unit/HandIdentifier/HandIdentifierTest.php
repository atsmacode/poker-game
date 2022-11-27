<?php

namespace Tests\Unit\HandIdentifier;

use App\Classes\HandIdentifier\HandIdentifier;
use Atsmacode\CardGames\Constants\Card;
use App\Constants\HandType;
use Atsmacode\CardGames\Factory\CardFactory;
use Tests\BaseTest;

class HandIdentifierTest extends BaseTest
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->handIdentifier = new HandIdentifier();
    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_the_card_with_the_highest_rank()
    {
        $highestCard = CardFactory::create(Card::KING_SPADES);

        $wholeCards = [
            CardFactory::create(Card::DEUCE_SPADES),
            $highestCard,
        ];

        $communityCards = [
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::SEVEN_DIAMONDS),
            CardFactory::create(Card::TEN_CLUBS),
            CardFactory::create(Card::THREE_SPADES),
            CardFactory::create(Card::FOUR_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals('High Card', $this->handIdentifier->identifiedHandType['handType']['name']);

        $this->assertEquals(
            $highestCard['ranking'],
            $this->handIdentifier->highCard
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_an_ace_as_the_card_with_the_highest_rank()
    {
        $highestCard = CardFactory::create(Card::ACE_SPADES);

        $wholeCards = [
            $highestCard,
            CardFactory::create(Card::KING_DIAMONDS)
        ];

        $communityCards = [
            CardFactory::create(Card::QUEEN_CLUBS),
            CardFactory::create(Card::FOUR_SPADES),
            CardFactory::create(Card::TEN_DIAMONDS),
            CardFactory::create(Card::DEUCE_CLUBS),
            CardFactory::create(Card::EIGHT_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals('High Card', $this->handIdentifier->identifiedHandType['handType']['name']);

        $this->assertEquals(
            14,
            $this->handIdentifier->highCard
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_a_pair()
    {
        $wholeCards = [
            CardFactory::create(Card::ACE_CLUBS),
            CardFactory::create(Card::KING_DIAMONDS),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::JACK_DIAMONDS),
            CardFactory::create(Card::FOUR_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::SEVEN_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);
        $this->assertEquals(HandType::PAIR['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertCount(1, $this->handIdentifier->pairs);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_two_pair()
    {
        $wholeCards = [
            CardFactory::create(Card::ACE_SPADES),
            CardFactory::create(Card::KING_SPADES),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::KING_HEARTS),
            CardFactory::create(Card::TEN_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::EIGHT_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);
        $this->assertEquals(HandType::TWO_PAIR['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertCount(2, $this->handIdentifier->pairs);
    }
}
