<?php

namespace Tests\Unit;

use App\Classes\HandIdentifier;
use App\Models\Card;

class HandIdentifierKickerTest extends BaseTest
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
    public function it_can_identify_the_kicker_and_active_ranks_for_a_high_card_hand()
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

        $this->assertEquals(
            14,
            $this->handIdentifier->highCard
        );

        $this->assertEquals(
            (new Card([
                'rank' => 'King',
                'suit' => 'Spades'
            ]))->ranking,
            $this->handIdentifier->identifiedHandType['kicker']
        );

        $this->assertContains(
            14,
            $this->handIdentifier->identifiedHandType['activeCards']
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_can_identify_the_kicker_and_active_ranks_for_a_pair()
    {
        $wholeCards = [
            new Card([
                'rank' => 'King',
                'suit' => 'Spades'
            ]),
            new Card([
                'rank' => 'Nine',
                'suit' => 'Diamonds'
            ]),
        ];

        $communityCards = [
            new Card([
                'rank' => 'Queen',
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

        $this->assertEquals(
            (new Card([
                'rank' => 'King',
                'suit' => 'Spades'
            ]))->ranking,
            $this->handIdentifier->identifiedHandType['kicker']
        );

        $this->assertContains(
            (new Card([
                'rank' => 'Nine',
                'suit' => 'Diamonds'
            ]))->ranking,
            $this->handIdentifier->identifiedHandType['activeCards']
        );

    }
}
