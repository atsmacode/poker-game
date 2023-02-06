<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\ShowdownRankingAndKicker;

use Atsmacode\PokerGame\HandStep\Start;
use Atsmacode\CardGames\Constants\Card;
use Atsmacode\PokerGame\Constants\HandType;
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

    private HandStreetCard $handStreetCardModel;

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
    public function highCardKingBeatsHighCardQueen()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player'  => $this->playerThree,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player'  => $this->playerThree,
                'card_id' => Card::THREE_DIAMONDS_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::SEVEN_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::FOUR_CLUBS_ID
            ],
            [
                'card_id' => Card::JACK_SPADES_ID
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
            'card_id' => Card::TEN_SPADES_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::HIGH_CARD['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function aceKingBeatsAceQueen()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player'  => $this->playerThree,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player'  => $this->playerThree,
                'card_id' => Card::ACE_DIAMONDS_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::KING_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::FOUR_CLUBS_ID
            ],
            [
                'card_id' => Card::JACK_SPADES_ID
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
            'card_id' => Card::THREE_HEARTS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::HIGH_CARD['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function kingHighStraightBeatsQueenHighStraight()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player'  => $this->playerThree,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player'  => $this->playerThree,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'player'  => $this->playerOne,
                'card_id' => Card::JACK_SPADES_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::JACK_HEARTS_ID
            ],
            [
                'card_id' => Card::TEN_CLUBS_ID
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
            'card_id' => Card::EIGHT_SPADES_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::STRAIGHT['id'], $response['winner']['handType']['id']);
    }

    protected function setflop($flopCards)
    {
        $flop = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[1]['name']])->getId(),
            'hand_id'   => $this->gameState->handId()
        ]);

        foreach($flopCards as $card){
            $this->handStreetCardModel->create([
                'hand_street_id' => $flop->getId(),
                'card_id'        => $card['card_id']
            ]);
        }
    }

    protected function setTurn($turnCard)
    {
        $turn = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[2]['name']])->getId(),
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->handStreetCardModel->create([
            'hand_street_id' => $turn->getId(),
            'card_id'        => $turnCard['card_id']
        ]);
    }

    protected function setRiver($riverCard)
    {
        $river = $this->handStreetModel->create([
            'street_id' => $this->streetModel->find(['name' => $this->gameState->getGame()->streets[3]['name']])->getId(),
            'hand_id'   => $this->gameState->handId()
        ]);

        $this->handStreetCardModel->create([
            'hand_street_id' => $river->getId(),
            'card_id'        => $riverCard['card_id']
        ]);
    }

    protected function executeActionsToContinue()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        return $this->setPlayerThreeChecksPost();
    }
}
