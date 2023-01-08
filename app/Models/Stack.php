<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Stack extends Model
{
    use Collection;

    public $table = 'stacks';
    public int $id;
    public int $amount;
    public int $player_id;
    public int $table_id;

    public function change(int $amount, int $playerId, int $tableId)
    {
        $query = sprintf("
            UPDATE
                stacks AS s
            SET
                s.amount = :amount
            WHERE
                s.table_id = :table_id
            AND
                s.player_id = :player_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':table_id', $tableId);
            $stmt->bindParam(':player_id', $playerId);
            $stmt->bindParam(':amount', $amount);
            $stmt->executeQuery();

            return true;
        } catch(\PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
