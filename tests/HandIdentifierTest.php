<?php

namespace Tests;

use App\Classes\HandIdentifier;
use App\Models\Card;
use PHPUnit\Framework\TestCase;

class HandIdentifierTest extends TestCase
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

        $highestCard = new Card('King', 'Spades');

        $wholeCards = [
            new Card('Deuce', 'Spades'),
            $highestCard,
        ];

        $communityCards = [
            new Card('Queen', 'Hearts'),
            new Card('Seven', 'Diamonds'),
            new Card('Ten', 'Clubs'),
            new Card('Three', 'Spades'),
            new Card('Four', 'Diamonds'),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals('High Card', $this->handIdentifier->identifiedHandType['handType']->name);

        $this->assertEquals(
            $highestCard->ranking,
            $this->handIdentifier->highCard
        );

    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_an_ace_as_the_card_with_the_highest_rank()
    {

        $highestCard = new Card('Ace', 'Spades');

        $wholeCards = [
            $highestCard,
            new Card('King', 'Diamonds')
        ];

        $communityCards = [
            new Card('Queen', 'Clubs'),
            new Card('Four', 'Spades'),
            new Card('Ten', 'Diamonds'),
            new Card('Deuce', 'Clubs'),
            new Card('Eight', 'Hearts'),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals('High Card', $this->handIdentifier->identifiedHandType['handType']->name);

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
            new Card('Ace', 'Spades'),
            new Card('King', 'Diamonds'),
        ];

        $communityCards = [
            new Card('Ace', 'Hearts'),
            new Card('Jack', 'Diamonds'),
            new Card('Four', 'Diamonds'),
            new Card('Nine', 'Clubs'),
            new Card('Seven', 'Diamonds'),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);
        $this->assertEquals('Pair', $this->handIdentifier->identifiedHandType['handType']->name);
        $this->assertCount(1, $this->handIdentifier->pairs);

    }

}
