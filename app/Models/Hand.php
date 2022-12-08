<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Hand extends Model
{
    use Collection;

    protected $table = 'hands';
    public    $id;

    public function streets()
    {
        $query = sprintf("
            SELECT
                hs.*
            FROM
                hand_streets AS hs
            LEFT JOIN
                hands AS h ON hs.hand_id = h.id
            WHERE
                hs.hand_id = :hand_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $this->id);

            $results = $stmt->executeQuery();
            $rows    = $results->fetchAllAssociative();

            return $rows;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function table()
    {
        return Table::find(['id' => $this->table_id]);
    }

    public function actions()
    {
        return PlayerAction::find(['hand_id' => $this->id]);
    }

    public function pot()
    {
        $query = sprintf("
            SELECT
                p.*
            FROM
                pots AS p
            LEFT JOIN
                hands AS h ON p.hand_id = h.id
            WHERE
                p.hand_id = :hand_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $this->id);

            $results = $stmt->executeQuery();
            $rows    = $results->fetchAssociative();

            return $rows;
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

    public static function latest()
    {
        return (new static())->latestQuery();
    }

    private function latestQuery()
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

    public function getNullActions()
    {
        $query = sprintf("
                SELECT
                    *
                FROM
                    player_actions
                WHERE
                    hand_id = :hand_id
                AND
                    action_id IS NULL
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $this->id);

            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
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
}
