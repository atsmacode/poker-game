<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Table extends Model
{
    use Collection;

    protected     $table = 'tables';
    public string $name;
    public        $content;
    public        $id;

    public function getSeats(int $tableId = null): array
    {
        $tableId = $tableId ?? $this->id;
        $query   = sprintf("
            SELECT
                *
            FROM
                table_seats
            WHERE
                table_id = :table_id 
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':table_id', $tableId);

            $results = $stmt->executeQuery();

            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
