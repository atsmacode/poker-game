<?php

namespace App\Models;

use PDO;
use PDOException;

class HandStreetCard extends Model
{

    use Collection;

    protected $table = 'hand_street_cards';

    public function card()
    {
        return Card::find(['id' => $this->card_id]);
    }

    public function getCard()
    {
        return $this->getCardQuery();
    }

    private function getCardQuery()
    {
        $query = sprintf("
            SELECT
                c.*, 
                r.name AS 'rank',
                r.abbreviation AS rankAbbreviation,
                s.name AS suit,
                s.abbreviation AS suitAbbreviation,
                r.ranking AS ranking 
            FROM
                hand_street_cards AS hsc
            LEFT JOIN
                cards AS c ON hsc.card_id = c.id
            LEFT OUTER JOIN 
                ranks r ON c.rank_id = r.id
            LEFT OUTER JOIN 
                suits s ON c.suit_id = s.id
            WHERE
                hsc.id = :id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

}
