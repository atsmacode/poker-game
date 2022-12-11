<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\Showdown;

use Atsmacode\CardGames\Constants\Card;
use Atsmacode\PokerGame\Constants\HandType;
use Atsmacode\PokerGame\HandStep\Start;
use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;
use Atsmacode\PokerGame\Tests\HasStreets;

/**
 * In these tests, we are not calling GamePlay->start()
 * as we need to set specific whole cards.
 */
class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay, HasActionPosts, HasStreets;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();

        $this->start               = $this->container->build(Start::class);
        $this->handStreetCardModel = $this->container->build(HandStreetCard::class);
    }

   /**
     * @test
     * @return void
     */
    public function a_pair_beats_high_card()
    { 
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->player3,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player' => $this->player3,
                'card_id' => Card::SIX_DIAMONDS_ID
            ],
            [
                'player' => $this->player1,
                'card_id' => Card::SIX_HEARTS_ID
            ],
            [
                'player' => $this->player1,
                'card_id' => Card::SEVEN_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::KING_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'card_id' => Card::DEUCE_CLUBS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::NINE_DIAMONDS_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::THREE_SPADES_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $this->executeActionsToContinue();

        $response = $this->actionControllerResponse();

        $this->assertEquals($this->player3->id, $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::PAIR['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function two_pair_beats_a_pair()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->player3,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player' => $this->player3,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player' => $this->player1,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->player1,
                'card_id' => Card::SEVEN_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::KING_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'card_id' => Card::DEUCE_CLUBS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::NINE_DIAMONDS_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::THREE_SPADES_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $this->executeActionsToContinue();

        $response = $this->actionControllerResponse();

        $this->assertEquals($this->player3->id, $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::TWO_PAIR['id'], $response['winner']['handType']['id']);
    }

    protected function setflop($flopCards)
    {
        $flop = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[1]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        foreach($flopCards as $card){
            $this->handStreetCardModel->create([
                'hand_street_id' => $flop->id,
                'card_id'        => $card['card_id']
            ]);
        }
    }

    protected function setTurn($turnCard)
    {
        $turn = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[2]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->handStreetCardModel->create([
            'hand_street_id' => $turn->id,
            'card_id'        => $turnCard['card_id']
        ]);
    }

    protected function setRiver($riverCard)
    {
        $river = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[3]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->handStreetCardModel->create([
            'hand_street_id' => $river->id,
            'card_id'        => $riverCard['card_id']
        ]);
    }

    protected function executeActionsToContinue()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->setPlayerThreeChecksPost();
    }
}
