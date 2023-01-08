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

    public function change(int $amount, int $playerId, int $tableId): int
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->update('stacks')
                ->set('amount', $queryBuilder->createNamedParameter($amount))
                ->where('table_id = ' . $queryBuilder->createNamedParameter($tableId))
                ->andWhere('player_id = ' . $queryBuilder->createNamedParameter($playerId));

            return $queryBuilder->executeStatement();
        } catch(\PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
