<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

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

    public static function playerAfterDealer($handId, $dealer)
    {
        return (new static())->playerAfterDealerQuery($handId, $dealer);
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
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':dealer', $dealer);

            $results       = $stmt->executeQuery();
            $rows          = $results->fetchAllAssociative();
            $this->content = $rows;

            $this->setModelProperties($rows);

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
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
            $stmt->bindParam(':hand_id', $handId);
            $stmt->executeQuery();

            return true;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public static function getContinuingPlayerSeats($handId)
    {
        return (new static())->getContinuingPlayerSeatsQuery($handId);
    }

    private function getContinuingPlayerSeatsQuery($handId)
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
            $stmt->bindParam(':hand_id', $handId);

            $results       = $stmt->executeQuery();
            $rows          = $results->fetchAllAssociative();
            $this->content = $rows;

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public static function getContinuingBetters($handId)
    {
        return (new static())->getContinuingBettersQuery($handId);
    }

    private function getContinuingBettersQuery($handId)
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
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':raise_id', $raiseId);
            $stmt->bindParam(':bet_id', $betId);
            $stmt->bindParam(':call_id', $callId);

            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
