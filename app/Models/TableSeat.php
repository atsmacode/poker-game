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

    private function playerAfterDealerQuery($handId, $firstActivePlayer)
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
                ts.id > :first_active_player
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':first_active_player', $firstActivePlayer);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function firstActivePlayer($handId, $firstActivePlayer)
    {
        return (new static())->firstActivePlayerQuery($handId, $firstActivePlayer);
    }

    private function firstActivePlayerQuery($handId, $firstActivePlayer)
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
                ts.id != :first_active_player
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':first_active_player', $firstActivePlayer);
            $stmt->execute();

            return $stmt->fetch();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}