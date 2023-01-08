<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Table extends Model
{
    use Collection;

    protected     $table = 'tables';
    public int    $id;
    public string $name;
    public int    $seats;

    public function getSeats(int $tableId = null): array
    {
        $tableId = $tableId ?? $this->id;

        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('table_seats', 'hs')
                ->where('table_id = ' . $queryBuilder->createNamedParameter($tableId));

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
