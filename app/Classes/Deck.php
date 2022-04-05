<?php

namespace App\Classes;

use App\Models\Card;
use App\Traits\Connect;
use PDO;
use PDOException;

class Deck extends Database
{

    use Connect;

    public $cards = [];

    public function __construct()
    {
        parent::__construct();
        $this->setCredentials();
        $this->cards = $this->compileDeck();
    }

    private function compileDeck()
    {
        foreach($this->selectAllCards() as $card){
            $this->cards[] = new Card([
                'rank' => $card['rank'],
                'suit' => $card['suit']
            ]);
        }
        return $this->cards;
    }

    private function selectAllCards()
    {
        $rows = null;

        try {

            $stmt = $this->connection->prepare("
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

        $this->connection = null;

        return $rows;

    }

}