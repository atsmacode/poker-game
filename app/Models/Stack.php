<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Stack extends Model
{
    use Collection, CanBeModelled;

    public $table = 'stacks';
    public string $name;

    public static function change(int $amount, int $playerId, int $tableId)
    {
        return (new static())->changeQuery($amount, $playerId, $tableId);
    }

    private function changeQuery(int $amount, int $playerId, int $tableId)
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