<?php

namespace Tests\Unit\HandIdentifier;

use App\Classes\HandIdentifier\HandIdentifier;
use App\Constants\Card;
use App\Factory\CardFactory;
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
            CardFactory::create(Card::KING_SPADES)
        ];

        $communityCards = [
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::SEVEN_DIAMONDS),
            CardFactory::create(Card::TEN_CLUBS),
            CardFactory::create(Card::THREE_SPADES),
            CardFactory::create(Card::FOUR_DIAMONDS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(
            13,
            $this->handIdentifier->highCard
        );

        $this->assertEquals(
            CardFactory::create(Card::QUEEN_HEARTS)['ranking'],
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
            CardFactory::create(Card::KING_SPADES),
            CardFactory::create(Card::NINE_DIAMONDS),
        ];

        $communityCards = [
            CardFactory::create(Card::QUEEN_HEARTS),
            CardFactory::create(Card::JACK_DIAMONDS),
            CardFactory::create(Card::FOUR_HEARTS),
            CardFactory::create(Card::NINE_CLUBS),
            CardFactory::create(Card::SEVEN_HEARTS),
        ];

        $this->handIdentifier->identify($wholeCards, $communityCards);

        $this->assertEquals(
            CardFactory::create(Card::KING_SPADES)['ranking'],
            $this->handIdentifier->identifiedHandType['kicker']
        );

        $this->assertContains(
            CardFactory::create(Card::NINE_DIAMONDS)['ranking'],
            $this->handIdentifier->identifiedHandType['activeCards']
        );

        $this->assertContains(
            CardFactory::create(Card::NINE_CLUBS)['ranking'],
            $this->handIdentifier->identifiedHandType['activeCards']
        );
    }
}
