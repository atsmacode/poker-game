<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Hand extends Model
{
    use Collection;

    protected $table = 'hands';
    public    $id;

    public function streets(): array
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('hs.*')
                ->from('hand_streets', 'hs')
                ->leftJoin('hs', 'hands', 'h', 'hs.hand_id = h.id')
                ->where('hs.hand_id = ' . $queryBuilder->createNamedParameter($this->id));

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function pot(): array
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('p.*')
                ->from('pots', 'p')
                ->leftJoin('p', 'hands', 'h', 'p.hand_id = h.id')
                ->where('p.hand_id = ' . $queryBuilder->createNamedParameter($this->id));

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function complete()
    {
        $query = sprintf("
            UPDATE
                hands
            SET
                completed_on = NOW()
            WHERE
                id = {$this->id}
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->executeQuery();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function latest()
    {
        $query = sprintf("
            SELECT * FROM hands ORDER BY id DESC LIMIT 1
        ");

        try {
            $stmt    = $this->connection->prepare($query);
            $results = $stmt->executeQuery();
            $rows    = $results->fetchAllAssociative();

            $this->setModelProperties($rows);

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getDealer()
    {
        $query = sprintf("
            SELECT
                *
            FROM
                player_actions AS pa
            LEFT JOIN
                table_seats AS ts ON pa.table_seat_id = ts.id
            WHERE
                pa.hand_id = :hand_id
            AND
                ts.is_dealer = 1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $this->id);
            
            $results = $stmt->executeQuery();
            $rows    = $results->fetchAllAssociative();

            $this->setModelProperties($rows);

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getPlayers(int $handId): array
    {
        $query = sprintf("
            SELECT
                ts.can_continue,
                ts.is_dealer,
                ts.player_id,
                ts.table_id,
                pa.bet_amount,
                pa.active,
                pa.has_acted,
                pa.big_blind,
                pa.small_blind,
                pa.action_id,
                pa.hand_id,
                pa.hand_street_id,
                pa.id AS player_action_id,
                ts.id AS table_seat_id,
                s.amount AS stack,
                a.name AS actionName,
                p.name AS playerName
            FROM
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            LEFT JOIN
                players AS p ON pa.player_id = p.id
            LEFT JOIN
                stacks AS s ON pa.player_id = s.player_id AND ts.table_id = s.table_id
            LEFT JOIN
                actions AS a ON pa.action_id = a.id
            WHERE
                pa.hand_id = :hand_id
            ORDER BY
                ts.id ASC
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $handId);
            
            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getCommunityCards(int $handId = null)
    {
        $handId = $handId ?? $this->id;
        $query  = sprintf("
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
                hand_streets AS hs ON hsc.hand_street_id = hs.id
            LEFT JOIN
                hands AS h ON hs.hand_id = h.id
            LEFT JOIN
                cards AS c ON hsc.card_id = c.id
            LEFT OUTER JOIN 
                ranks r ON c.rank_id = r.id
            LEFT OUTER JOIN 
                suits s ON c.suit_id = s.id
            WHERE
                h.id = :id
            ORDER BY
                hsc.id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $handId);

            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
