<?php

namespace App\Models;

use PDO;
use PDOException;

class TableSeat extends Model
{

    use Collection;

    protected $table = 'table_seats';
    public string $name;
    public $player_id;

    public function player()
    {
        return Player::find(['id' => $this->player_id]);
    }

    public static function playerAfterDealer($handId, $firstActivePlayer)
    {
        return (new static())->playerAfterDealerQuery($handId, $firstActivePlayer);
    }

    private function playerAfterDealerQuery($handId, $dealer)
    {
        $query = sprintf("
            SELECT
                ts.*
            FROM
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            WHERE
                pa.hand_id = :hand_id
            AND
                pa.active = 1
            AND
                ts.id > :dealer
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':dealer', $dealer);
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $this->setModelProperties($rows);

            return $this;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function firstActivePlayer($handId, $dealer)
    {
        return (new static())->firstActivePlayerQuery($handId, $dealer);
    }

    private function firstActivePlayerQuery($handId, $dealer)
    {
        $query = sprintf("
            SELECT
                ts.*
            FROM
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            WHERE
                pa.hand_id = :hand_id
            AND
                pa.active = 1
            AND
                ts.id != :dealer
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':dealer', $dealer);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function bigBlindWins($handId)
    {
        return (new static())->bigBlindWinsQuery($handId);
    }

    private function bigBlindWinsQuery($handId)
    {
        $query = sprintf("
            UPDATE
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            SET
                ts.can_continue = 1
            WHERE
                pa.hand_id = :hand_id
            AND
                pa.active = 1
            AND
                pa.big_blind = 1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->execute();

            return true;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function firstPlayer($handId)
    {
        return (new static())->firstPlayerQuery($handId);
    }

    private function firstPlayerQuery($handId)
    {
        $query = sprintf("
            SELECT
                ts.*
            FROM
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            WHERE
                pa.hand_id = :hand_id
            AND
                pa.active = 1
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $this->setModelProperties($rows);

            return $this;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

}
