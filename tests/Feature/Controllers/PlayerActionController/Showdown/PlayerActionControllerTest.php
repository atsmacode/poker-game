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

    private Start          $start;
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
    public function pairBeatsHighCard()
    { 
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::SIX_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::SIX_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
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

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::PAIR['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function twoPairBeatsPair()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
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

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::TWO_PAIR['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function tripsBeatsTwoPair()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_CLUBS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::QUEEN_HEARTS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::SEVEN_CLUBS_ID
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

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::TRIPS['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function straightBeatsTrips()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_CLUBS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::QUEEN_HEARTS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::TEN_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'card_id' => Card::KING_SPADES_ID
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

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::STRAIGHT['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function flushBeatsStraight()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::NINE_CLUBS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::TEN_DIAMONDS_ID
            ],
            [
                'card_id' => Card::JACK_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_CLUBS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::SEVEN_DIAMONDS_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::THREE_DIAMONDS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::FLUSH['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function fullHouseBeatsFlush()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_CLUBS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::NINE_CLUBS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::JACK_SPADES_ID
            ],
            [
                'card_id' => Card::JACK_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_CLUBS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::SEVEN_CLUBS_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::THREE_CLUBS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::FULL_HOUSE['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function quadsBeatsFullHouse()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_HEARTS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_DIAMONDS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::KING_CLUBS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::JACK_SPADES_ID
            ],
            [
                'card_id' => Card::JACK_CLUBS_ID
            ],
            [
                'card_id' => Card::QUEEN_CLUBS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::KING_SPADES_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::QUEEN_HEARTS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::QUADS['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function straightFlushBeatsQuads()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::JACK_HEARTS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::QUEEN_CLUBS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::QUEEN_DIAMONDS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::TEN_HEARTS_ID
            ],
            [
                'card_id' => Card::NINE_HEARTS_ID
            ],
            [
                'card_id' => Card::QUEEN_HEARTS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::QUEEN_SPADES_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::DEUCE_CLUBS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::STRAIGHT_FLUSH['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function royalFlushBeatsStraightFlush()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player' => $this->playerThree,
                'card_id' => Card::ACE_HEARTS_ID
            ],
            [
                'player' => $this->playerThree,
                'card_id' => Card::KING_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::SEVEN_HEARTS_ID
            ],
            [
                'player' => $this->playerOne,
                'card_id' => Card::EIGHT_HEARTS_ID
            ],
        ];

        $this->setWholeCards($wholeCards);

        $flopCards = [
            [
                'card_id' => Card::TEN_HEARTS_ID
            ],
            [
                'card_id' => Card::NINE_HEARTS_ID
            ],
            [
                'card_id' => Card::QUEEN_HEARTS_ID
            ]
        ];

        $this->setFlop($flopCards);

        $turnCard = [
            'card_id' => Card::JACK_HEARTS_ID
        ];

        $this->setTurn($turnCard);

        $riverCard = [
            'card_id' => Card::DEUCE_CLUBS_ID
        ];

        $this->setRiver($riverCard);

        $this->gameState->setPlayers();

        $request  = $this->executeActionsToContinue();
        $response = $this->actionControllerResponse($request);

        $this->assertEquals($this->playerThree->getId(), $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::ROYAL_FLUSH['id'], $response['winner']['handType']['id']);
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
