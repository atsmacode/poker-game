<?php

namespace App\Classes;

use App\Models\Card;
use App\Traits\Connect;
use PDO;
use PDOException;

class Deck
{

    use Connect;

    public $cards = [];

    public function __construct()
    {
        $this->setCredentials();
        $this->cards = $this->compileDeck();
    }

    private function compileDeck()
    {
        foreach($this->selectAllCards() as $card){
            $this->cards[] = new Card($card['rank'], $card['suit']);
        }
        return $this->cards;
    }

    private function selectAllCards()
    {
        $rows = null;

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT r.name as rank, s.name as suit, r.ranking as ranking FROM cards c
                    LEFT OUTER JOIN ranks r ON c.rank_id = r.id
                    LEFT OUTER JOIN suits s ON c.suit_id = s.id
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $conn = null;

        return $rows;

    }

}