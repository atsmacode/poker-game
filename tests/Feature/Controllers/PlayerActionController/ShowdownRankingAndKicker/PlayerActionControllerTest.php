<?php

namespace Atsmacode\PokerGame\Tests\Feature\Controllers\PlayerActionController\ShowdownRankingAndKicker;

use Atsmacode\PokerGame\Classes\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GamePlay\GamePlay;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
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
use Atsmacode\PokerGame\Tests\Feature\HasActionPosts;
use Atsmacode\PokerGame\Tests\Feature\HasGamePlay;
use Atsmacode\PokerGame\Tests\Feature\HasStreets;

/**
 * In these tests, we are not calling GamePlay->start()
 * as we need to set specific whole cards.
 */
class PlayerActionControllerTest extends BaseTest
{
    use HasGamePlay;
    use HasActionPosts;
    use HasStreets;

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

        $this->gameState = new GameState(new GameData(), $this->hand);
        $this->gamePlay  = new GamePlay(
            $this->gameState,
            new Start(),
            new NewStreet(),
            new Showdown(),
            new PlayerHandler()
        );

        $this->start         = new Start($this->gameState->getGame(), $this->gameState->getGameDealer());
        $this->actionHandler = new ActionHandler($this->gameState);
    }

   /**
     * @test
     * @return void
     */
    public function high_card_king_beats_high_card_queen()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player'  => $this->player3,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player'  => $this->player3,
                'card_id' => Card::THREE_DIAMONDS_ID
            ],
            [
                'player'  => $this->player1,
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'player'  => $this->player1,
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

        $this->executeActionsToContinue();

        $response = $this->jsonResponse();

        $this->assertEquals($this->player3->id, $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::HIGH_CARD['id'], $response['winner']['handType']['id']);
    }

    /**
     * @test
     * @return void
     */
    public function ace_king_beats_king_queen()
    {
        $this->start->setGameState($this->gameState)
            ->initiateStreetActions()
            ->initiatePlayerStacks()
            ->setDealerAndBlindSeats()
            ->getGameState();

        $wholeCards = [
            [
                'player'  => $this->player3,
                'card_id' => Card::KING_SPADES_ID
            ],
            [
                'player'  => $this->player3,
                'card_id' => Card::ACE_DIAMONDS_ID
            ],
            [
                'player'  => $this->player1,
                'card_id' => Card::QUEEN_SPADES_ID
            ],
            [
                'player'  => $this->player1,
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

        $this->executeActionsToContinue();

        $response = $this->jsonResponse();

        $this->assertEquals($this->player3->id, $response['winner']['player']['player_id']);
        $this->assertEquals(HandType::HIGH_CARD['id'], $response['winner']['handType']['id']);
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
