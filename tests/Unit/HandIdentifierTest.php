<?php

namespace Tests\Unit;

use App\Classes\HandIdentifier;
use App\Models\Card;

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

        $highestCard = new Card([
            'rank' => 'King',
            'suit' => 'Spades'
        ]);

        $wholeCards = [
            new Card([
                'rank' => 'Deuce',
                'suit' => 'Spades'
            ]),
            $highestCard,
        ];

        $communityCards = [
            new Card([
                'rank' => 'Queen',
                'suit' => 'Hearts'
            ]),
            new Card([
                'rank' => 'Seven',
                'suit' => 'Diamonds'
            ]),
            new Card([
                'rank' => 'Ten',
                'suit' => 'Clubs'
            ]),
            new Card([
                'rank' => 'Three',
                'suit' => 'Spades'
            ]),
            new Card([
                'rank' => 'Four',
                'suit' => 'Diamonds'
            ]),
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

        $highestCard = new Card([
            'rank' => 'Ace',
            'suit' => 'Spades'
        ]);

        $wholeCards = [
            $highestCard,
            new Card([
                'rank' => 'King',
                'suit' => 'Diamonds'
            ])
        ];

        $communityCards = [
            new Card([
                'rank' => 'Queen',
                'suit' => 'Clubs'
            ]),
            new Card([
                'rank' => 'Four',
                'suit' => 'Spades'
            ]),
            new Card([
                'rank' => 'Ten',
                'suit' => 'Diamonds'
            ]),
            new Card([
                'rank' => 'Deuce',
                'suit' => 'Clubs'
            ]),
            new Card([
                'rank' => 'Eight',
                'suit' => 'Hearts'
            ]),
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
            new Card([
                'rank' => 'Ace',
                'suit' => 'Spades'
            ]),
            new Card([
                'rank' => 'King',
                'suit' => 'Diamonds'
            ]),
        ];

        $communityCards = [
            new Card([
                'rank' => 'Ace',
                'suit' => 'Hearts'
            ]),
            new Card([
                'rank' => 'Jack',
                'suit' => 'Diamonds'
            ]),
            new Card([
                'rank' => 'Four',
                'suit' => 'Diamonds'
            ]),
            new Card([
                'rank' => 'Nine',
                'suit' => 'Clubs'
            ]),
            new Card([
                'rank' => 'Seven',
                'suit' => 'Diamonds'
            ]),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);
        $this->assertEquals('Pair', $this->handIdentifier->identifiedHandType['handType']->name);
        $this->assertCount(1, $this->handIdentifier->pairs);

    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_two_pair()
    {
        $wholeCards = [
            new Card([
                'rank' => 'Ace',
                'suit' => 'Spades'
            ]),
            new Card([
                'rank' => 'King',
                'suit' => 'Spades'
            ]),
        ];

        $communityCards = [
            new Card([
                'rank' => 'Ace',
                'suit' => 'Hearts'
            ]),
            new Card([
                'rank' => 'King',
                'suit' => 'Hearts'
            ]),
            new Card([
                'rank' => 'Ten',
                'suit' => 'Diamonds'
            ]),
            new Card([
                'rank' => 'Nine',
                'suit' => 'Clubs'
            ]),
            new Card([
                'rank' => 'Eight',
                'suit' => 'Diamonds'
            ]),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);
        $this->assertEquals('Two Pair', $this->handIdentifier->identifiedHandType['handType']->name);
        $this->assertCount(2, $this->handIdentifier->pairs);
    }

}
