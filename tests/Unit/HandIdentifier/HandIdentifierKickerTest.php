<?php

namespace Tests\Unit\HandIdentifier;

use App\Classes\HandIdentifier\HandIdentifier;
use App\Models\Card;
use App\Constants\Card as ConstantsCard;
use Tests\BaseTest;

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
            new Card(ConstantsCard::KING_SPADES)
        ];

        $communityCards = [
            new Card(ConstantsCard::QUEEN_HEARTS),
            new Card(ConstantsCard::SEVEN_DIAMONDS),
            new Card(ConstantsCard::TEN_CLUBS),
            new Card(ConstantsCard::THREE_SPADES),
            new Card(ConstantsCard::FOUR_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(
            13,
            $this->handIdentifier->highCard
        );

        $this->assertEquals(
            (new Card(ConstantsCard::QUEEN_HEARTS))->ranking,
            $this->handIdentifier->identifiedHandType['kicker']
        );

        $this->assertContains(
            13,
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
            new Card(ConstantsCard::KING_SPADES),
            new Card(ConstantsCard::NINE_DIAMONDS),
        ];

        $communityCards = [
            new Card(ConstantsCard::QUEEN_HEARTS),
            new Card(ConstantsCard::JACK_DIAMONDS),
            new Card(ConstantsCard::FOUR_HEARTS),
            new Card(ConstantsCard::NINE_CLUBS),
            new Card(ConstantsCard::SEVEN_HEARTS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(
            (new Card(ConstantsCard::KING_SPADES))->ranking,
            $this->handIdentifier->identifiedHandType['kicker']
        );

        $this->assertContains(
            (new Card(ConstantsCard::NINE_DIAMONDS))->ranking,
            $this->handIdentifier->identifiedHandType['activeCards']
        );

        $this->assertContains(
            (new Card(ConstantsCard::NINE_CLUBS))->ranking,
            $this->handIdentifier->identifiedHandType['activeCards']
        );
    }
}
