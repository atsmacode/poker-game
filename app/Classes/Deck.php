<?php

namespace App\Classes;

use App\Constants\Card as Constants;
use App\Models\Card;
use App\Traits\Connect;

class Deck extends Database
{
    use Connect;

    public $cards = [
        Constants::ACE_CLUBS,
        Constants::DEUCE_CLUBS,
        Constants::THREE_CLUBS,
        Constants::FOUR_CLUBS,
        Constants::FIVE_CLUBS,
        Constants::SIX_CLUBS,
        Constants::SEVEN_CLUBS,
        Constants::EIGHT_CLUBS,
        Constants::NINE_CLUBS,
        Constants::TEN_CLUBS,
        Constants::JACK_CLUBS,
        Constants::QUEEN_CLUBS,
        Constants::KING_CLUBS,
        Constants::ACE_DIAMONDS,
        Constants::DEUCE_DIAMONDS,
        Constants::THREE_DIAMONDS,
        Constants::FOUR_DIAMONDS,
        Constants::FIVE_DIAMONDS,
        Constants::SIX_DIAMONDS,
        Constants::SEVEN_DIAMONDS,
        Constants::EIGHT_DIAMONDS,
        Constants::NINE_DIAMONDS,
        Constants::TEN_DIAMONDS,
        Constants::JACK_DIAMONDS,
        Constants::QUEEN_DIAMONDS,
        Constants::KING_DIAMONDS,
        Constants::ACE_HEARTS,
        Constants::DEUCE_HEARTS,
        Constants::THREE_HEARTS,
        Constants::FOUR_HEARTS,
        Constants::FIVE_HEARTS,
        Constants::SIX_HEARTS,
        Constants::SEVEN_HEARTS,
        Constants::EIGHT_HEARTS,
        Constants::NINE_HEARTS,
        Constants::TEN_HEARTS,
        Constants::JACK_HEARTS,
        Constants::QUEEN_HEARTS,
        Constants::KING_HEARTS,
        Constants::ACE_SPADES,
        Constants::DEUCE_SPADES,
        Constants::THREE_SPADES,
        Constants::FOUR_SPADES,
        Constants::FIVE_SPADES,
        Constants::SIX_SPADES,
        Constants::SEVEN_SPADES,
        Constants::EIGHT_SPADES,
        Constants::NINE_SPADES,
        Constants::TEN_SPADES,
        Constants::JACK_SPADES,
        Constants::QUEEN_SPADES,
        Constants::KING_SPADES
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setCredentials();
        $this->cards = $this->compileDeck();
    }

    /**
     * As this class extends Database,
     * the connection properties are partially
     * overridden when serializing.
     *
     * @return array
     */
    // public function __serialize(): array
    // {
    //     parent::__serialize();
    //     $this->cards = (array) $this->cards;
        
    //     return (array) $this;
    // }

    // public function __unserialize(array $data): void
    // {
    //     parent::__unserialize($data);
    //     $this->cards = $data['cards'];
    // }

    private function compileDeck()
    {
        foreach ($this->cards as $key => $card) {
            $this->cards[$key] = new Card($card);
        }

        return $this->cards;
    }

    // private function selectAllCards()
    // {
    //     $rows = null;

    //     try {

    //         $stmt = $this->connection->prepare("
    //                 SELECT r.name as 'rank', s.name as suit, r.ranking as ranking FROM cards c
    //                 LEFT OUTER JOIN ranks r ON c.rank_id = r.id
    //                 LEFT OUTER JOIN suits s ON c.suit_id = s.id
    //             ");
    //         $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //         $stmt->execute();

    //         $rows = $stmt->fetchAll();

    //     } catch(PDOException $e) {
    //         echo $e->getMessage();
    //     }

    //     $this->connection = null;

    //     return $rows;
    // }
}
