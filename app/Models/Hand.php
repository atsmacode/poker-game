<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Hand extends Model
{
    use Collection, CanBeModelled;

    protected $table = 'hands';
    public $id;

    public function streets()
    {
        return HandStreet::find(['hand_id' => $this->id]);
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
        return Pot::find(['hand_id' => $this->id]);
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
