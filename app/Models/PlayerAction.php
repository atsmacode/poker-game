<?php

namespace App\Models;

use PDO;
use PDOException;

class PlayerAction extends Model
{

    use Collection;

    protected $table = 'player_actions';
    public $id;

    public function player()
    {
        return Player::find(['id' => $this->player_id]);
    }

    public function tableSeat()
    {
        return TableSeat::find(['id' => $this->table_seat_id]);
    }

    public function action()
    {
        return Action::find(['id' => $this->action_id]);
    }

    /**
     * May not bee needed, is in TableSeat
     */
    public static function playerAfterDealer($handId, $firstActivePlayer)
    {
        return (new static())->playerAfterDealerQuery($handId, $firstActivePlayer);
    }

    /**
     * May not bee needed, is in TableSeat
     */
    private function playerAfterDealerQuery($handId, $firstActivePlayer)
    {
        $query = sprintf("
            SELECT
                *
            FROM
                player_actions
            WHERE
                hand_id = :hand_id
            AND
                active = 1
            AND
                table_seat_id > :first_active_player
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
                *
            FROM
                player_actions
            WHERE
                hand_id = :hand_id
            AND
                active = 1
            AND
                table_seat_id != :first_active_player
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