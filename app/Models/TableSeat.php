<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\Orm\Classes\Collection;
use PDO;
use PDOException;

class TableSeat extends Model
{
    use Collection;

    protected     $table = 'table_seats';
    public string $name;
    public        $player_id;

    public function __construct($connection, array $data = null)
    {
        parent::__construct($connection, $data);
    }

    public function player()
    {
        return Player::find(['id' => $this->player_id]);
    }

    public function playerAfterDealer($handId, $dealer)
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

            $rows          = $stmt->fetchAll();
            $this->content = $rows;

            $this->setModelProperties($rows);

            return $this;
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function bigBlindWins($handId)
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
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getContinuingPlayerSeats($handId)
    {
        $query = sprintf("
            SELECT
                ts.*
            FROM
                player_actions AS pa
            LEFT JOIN
                table_seats AS ts ON pa.table_seat_id = ts.id
            WHERE
                pa.hand_id = :hand_id
            AND
                pa.active = 1
            AND
                ts.can_continue = 1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->execute();

            $this->content = $stmt->fetchAll();

            return $this;
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getContinuingBetters($handId)
    {
        $raiseId = Action::RAISE_ID;
        $betId   = Action::BET_ID;
        $callId  = Action::CALL_ID;

        $query = sprintf("
            SELECT
                ts.*
            FROM
                player_actions AS pa
            LEFT JOIN
                table_seats AS ts ON pa.table_seat_id = ts.id
            WHERE
                pa.hand_id = :hand_id
            AND
                ts.can_continue = 1
            AND
                pa.action_id IN (:raise_id, :bet_id, :call_id)
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':raise_id', $raiseId);
            $stmt->bindParam(':bet_id', $betId);
            $stmt->bindParam(':call_id', $callId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
