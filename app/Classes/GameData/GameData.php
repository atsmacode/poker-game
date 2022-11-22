<?php
namespace App\Classes\GameData;

use App\Classes\Database;

class GameData extends Database
{
    public static function getSeats($handId)
    {
        return (new static())->getSeatsQuery($handId);
    }

    private function getSeatsQuery($tableId)
    {

        $query = sprintf("
            SELECT
                *
            FROM
                table_seats
            WHERE
                table_id = :table_id 
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->bindParam(':table_id', $tableId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getPlayers($handId)
    {
        return (new static())->getPlayersQuery($handId);
    }

    private function getPlayersQuery($handId)
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
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }
}