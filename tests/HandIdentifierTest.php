<?php

namespace Tests;

use App\Classes\Card;
use App\Classes\Connect;
use App\Classes\HandIdentifier;
use PHPUnit\Framework\TestCase;

class HandIdentifierTest extends TestCase
{

    use Connect;

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

        $this->assertEquals('High Card', $this->handIdentifier->identifiedHandType['handType']['name']);

        $this->assertEquals(
            $highestCard->ranking,
            $this->handIdentifier->highCard
        );

    }

}
