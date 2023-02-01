<?php

namespace Atsmacode\PokerGame\Database\Seeders;

use Atsmacode\Framework\Database\Database;

class SeedTables extends Database
{
    public static array $methods = [
        'seed'
    ];

    private int $seats   = 6;
    private int $tableId = 1;

    public function seed(): void
    {
        $this->createTable();
    }

    private function createTable(): void
    {
        /** TODO: only supporting 1 table for the time being. */
        $name  = 'Table 1';

        try {
            $queryBuilder = $this->connection->createQueryBuilder();

            $queryBuilder
                ->insert('tables')
                ->setValue('name', $queryBuilder->createNamedParameter($name))
                ->setValue('seats', $queryBuilder->createNamedParameter($this->seats))
                ->setParameter($queryBuilder->createNamedParameter($name), $name)
                ->setParameter($queryBuilder->createNamedParameter($this->seats), $this->seats);

            $queryBuilder->executeStatement();

            $this->createTableSeats();
        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }

    private function createTableSeats(): void
    {
        try {
            $inserted = 0;

            while($inserted < $this->seats){
                $queryBuilder = $this->connection->createQueryBuilder();

                $queryBuilder
                    ->insert('table_seats')
                    ->setValue('table_id', $queryBuilder->createNamedParameter($this->tableId))
                    ->setParameter($queryBuilder->createNamedParameter($this->tableId), $this->tableId);

                $queryBuilder->executeStatement();

                $inserted++;
            }
        } catch(\Exception $e) {
            error_log($e->getMessage());;
        }
    }
}
