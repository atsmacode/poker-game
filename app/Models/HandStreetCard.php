<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class HandStreetCard extends Model
{
    use Collection;

    protected $table = 'hand_street_cards';

    public function getCard()
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
            $stmt->bindParam(':id', $this->id);

            $results = $stmt->executeQuery();

            return $results->fetchAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
