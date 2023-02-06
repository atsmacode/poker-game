<?php

namespace Atsmacode\PokerGame\Tests\Unit\HandIdentifier;

use Atsmacode\PokerGame\HandIdentifier\HandIdentifier;
use Atsmacode\CardGames\Constants\Card;
use Atsmacode\CardGames\Constants\Rank;
use Atsmacode\PokerGame\Constants\HandType;
use Atsmacode\CardGames\Factory\CardFactory;
use Atsmacode\PokerGame\Tests\BaseTest;

class HandIdentifierTest extends BaseTest
{
    private HandIdentifier $handIdentifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handIdentifier = new HandIdentifier();
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyTheCardWithTheHighestRank()
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
    public function itCanIdentifyAceAsTheHighestRankedCard()
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
    public function itCanIdentifyAPair()
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
    public function itCanIdentifyTwoPair()
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

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyTrips()
    {
        $wholeCards = [
            CardFactory::create(Card::DEUCE_CLUBS),
            CardFactory::create(Card::KING_SPADES),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::KING_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::EIGHT_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::TRIPS['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(Rank::KING, $this->handIdentifier->threeOfAKind);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyFiveHighStraight()
    {
        $wholeCards = [
            CardFactory::create(Card::FIVE_CLUBS),
            CardFactory::create(Card::FOUR_DIAMONDS),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::DEUCE_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::THREE_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::STRAIGHT['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(Rank::FIVE['ranking'], $this->handIdentifier->identifiedHandType['kicker']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAceHighStraight()
    {
        $wholeCards = [
            CardFactory::create(Card::JACK_CLUBS),
            CardFactory::create(Card::TEN_DIAMONDS),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::THREE_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::STRAIGHT['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(14, $this->handIdentifier->identifiedHandType['kicker']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAStraightWithADuplicateRank()
    {
        $wholeCards = [
            CardFactory::create(Card::JACK_CLUBS),
            CardFactory::create(Card::TEN_DIAMONDS),
        ];

        $communityCards = [
            CardFactory::create(Card::NINE_HEARTS),
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::JACK_HEARTS),
            CardFactory::create(Card::THREE_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::STRAIGHT['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(Rank::KING['ranking'], $this->handIdentifier->identifiedHandType['kicker']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAFlush()
    {
        $wholeCards = [
            CardFactory::create(Card::JACK_CLUBS),
            CardFactory::create(Card::TEN_CLUBS),
        ];

        $communityCards = [
            CardFactory::create(Card::DEUCE_CLUBS),
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::FIVE_CLUBS),
            CardFactory::create(Card::THREE_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::FLUSH['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAnAceHighFlush()
    {
        $wholeCards = [
            CardFactory::create(Card::JACK_CLUBS),
            CardFactory::create(Card::TEN_CLUBS),
        ];

        $communityCards = [
            CardFactory::create(Card::DEUCE_CLUBS),
            CardFactory::create(Card::ACE_CLUBS),
            CardFactory::create(Card::KING_CLUBS),
            CardFactory::create(Card::FIVE_CLUBS),
            CardFactory::create(Card::THREE_CLUBS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::FLUSH['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(14, $this->handIdentifier->identifiedHandType['kicker']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAFullHouse()
    {
        $wholeCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::KING_SPADES),
        ];

        $communityCards = [
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::ACE_DIAMONDS),
            CardFactory::create(Card::ACE_SPADES),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::EIGHT_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::FULL_HOUSE['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyQuads()
    {
        $wholeCards = [
            CardFactory::create(Card::KING_CLUBS),
            CardFactory::create(Card::KING_SPADES),
        ];

        $communityCards = [
            CardFactory::create(Card::ACE_HEARTS),
            CardFactory::create(Card::KING_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::EIGHT_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::QUADS['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
        $this->assertEquals(Rank::KING, $this->handIdentifier->fourOfAKind);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyAStraightFlush()
    {
        $wholeCards = [
            CardFactory::create(Card::KING_HEARTS),
            CardFactory::create(Card::QUEEN_HEARTS),
        ];

        $communityCards = [
            CardFactory::create(Card::NINE_HEARTS),
            CardFactory::create(Card::TEN_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::JACK_HEARTS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::STRAIGHT_FLUSH['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function itCanIdentifyARoyalFlush()
    {
        $wholeCards = [
            CardFactory::create(Card::KING_HEARTS),
            CardFactory::create(Card::QUEEN_HEARTS),
        ];

        $communityCards = [
            CardFactory::create(Card::JACK_HEARTS),
            CardFactory::create(Card::TEN_HEARTS),
            CardFactory::create(Card::KING_DIAMONDS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::ACE_HEARTS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(HandType::ROYAL_FLUSH['id'], $this->handIdentifier->identifiedHandType['handType']['id']);
    }
}
