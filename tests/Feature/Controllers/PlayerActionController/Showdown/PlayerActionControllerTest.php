<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\Showdown;

use Atsmacode\PokerGame\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\GameData\GameData;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\HandStep\Start;
use Atsmacode\CardGames\Constants\Card;
use Atsmacode\PokerGame\Constants\HandType;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\HandStreet;
use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Street;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasActionPosts;
use Atsmacode\PokerGame\Tests\HasGamePlay;
use Atsmacode\PokerGame\Tests\HasStreets;
use Atsmacode\PokerGame\Game\PotLimitHoldEm;

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

        $this->table         = Table::create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand          = Hand::create(['table_id' => $this->table->id]);

        $this->player1 = Player::create([
            'name' => 'Player 1',
            'email' => 'player1@rrh.com'
        ]);

        $this->player2 = Player::create([
            'name' => 'Player 2',
            'email' => 'player2@rrh.com'
        ]);

        $this->player3 = Player::create([
            'name' => 'Player 3',
            'email' => 'player3@rrh.com'
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        TableSeat::create([
            'table_id' => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->gameState = new GameState($this->container->get(GameData::class), $this->hand);
        $this->gamePlay  = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get(PotLimitHoldEm::class),
            'gameState' => $this->gameState
        ]);

        $this->actionHandler = new ActionHandler($this->gameState);
        $this->start         = new Start($this->gameState->getGame(), $this->gameState->getGameDealer());
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

        $response = $this->jsonResponse();

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

        $response = $this->jsonResponse();

        $this->assertEquals($this->player3->id, $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::TWO_PAIR['id'], $response['winner']['handType']['id']);
    }

    protected function setflop($flopCards)
    {
        $flop = HandStreet::create([
            'street_id' => Street::find(['name' => $this->gameState->getGame()->streets[1]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        foreach($flopCards as $card){
            HandStreetCard::create([
                'hand_street_id' => $flop->id,
                'card_id'        => $card['card_id']
            ]);
        }
    }

    protected function setTurn($turnCard)
    {
        $turn = HandStreet::create([
            'street_id' => Street::find(['name' => $this->gameState->getGame()->streets[2]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        HandStreetCard::create([
            'hand_street_id' => $turn->id,
            'card_id'        => $turnCard['card_id']
        ]);
    }

    protected function setRiver($riverCard)
    {
        $river = HandStreet::create([
            'street_id' => Street::find(['name' => $this->gameState->getGame()->streets[3]['name']])->id,
            'hand_id'   => $this->gameState->handId()
        ]);

        HandStreetCard::create([
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
