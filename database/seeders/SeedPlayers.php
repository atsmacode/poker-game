<?php

namespace Atsmacode\PokerGame\Database\Seeders;

use Atsmacode\Framework\Database\Database;

class SeedPlayers extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed(): void
    {
        $this->createPlayers();
    }

    private function createPlayers(): void
    {
        try {
            $seats    = 6;
            $inserted = 0;

            while($inserted < $seats){
                $seatId = $inserted + 1;
                $name = 'Player ' . $seatId;
                $email = 'player' . $seatId . '@rrh.com';

                $queryBuilder = $this->connection->createQueryBuilder();

                $queryBuilder
                    ->insert('players')
                    ->setValue('name', $queryBuilder->createNamedParameter($name))
                    ->setValue('email', $queryBuilder->createNamedParameter($email))
                    ->setParameter($queryBuilder->createNamedParameter($name), $name)
                    ->setParameter($queryBuilder->createNamedParameter($email), $email);

                $queryBuilder->executeStatement();

                $playerId = $this->connection->lastInsertId();

                $this->addPlayerToSeat($playerId, $seatId);

                $inserted++;
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }
    }

    private function addPlayerToSeat(int $playerId, int $seatId): void
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->update('table_seats')
                ->set('player_id', $queryBuilder->createNamedParameter($playerId))
                ->where('id = ' . $queryBuilder->createNamedParameter($seatId));

            $queryBuilder->executeStatement();
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
