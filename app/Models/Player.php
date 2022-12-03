<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;

class Player extends PokerGameModel
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

    public static function getWholeCards(int $handId, int $playerId)
    {
        return (new static())->wholeCardsQuery($handId, $playerId);
    }

    private function wholeCardsQuery(int $handId, int $playerId)
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
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':player_id', $playerId);

            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
