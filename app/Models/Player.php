<?php

namespace App\Models;

use PDO;
use PDOException;

class Player extends Model
{
    use Collection;

    protected $table = 'players';
    public $id;

    public function wholeCards()
    {
        return WholeCard::find(['player_id' => $this->id]);
    }

    public function actions()
    {
        return PlayerAction::find(['player_id' => $this->id]);
    }

    public function stacks()
    {
        return Stack::find(['player_id' => $this->id]);
    }

    public function getWholeCards($handId)
    {
        return $this->wholeCardsQuery($handId);
    }

    private function wholeCardsQuery($handId)
    {
        $query = sprintf("
            SELECT
                c.*, 
                wc.player_id,
                r.name AS 'rank',
                r.abbreviation AS rankAbbreviation,
                s.name AS suit,
                s.abbreviation AS suitAbbreviation,
                r.ranking AS ranking 
            FROM
                whole_cards AS wc
            LEFT JOIN
                cards AS c ON wc.card_id = c.id
            LEFT OUTER JOIN 
                ranks r ON c.rank_id = r.id
            LEFT OUTER JOIN 
                suits s ON c.suit_id = s.id
            WHERE
                wc.hand_id = :hand_id
            AND
                wc.player_id = :player_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':player_id', $this->id);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
