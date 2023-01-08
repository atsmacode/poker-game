<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class TableSeat extends Model
{
    use Collection;

    protected      $table = 'table_seats';
    public int     $id;
    public ?int    $number;
    public int     $can_continue;
    public int     $is_dealer;
    public int     $player_id;
    public int     $table_id;
    public ?string $updated_at;

    public function playerAfterDealer(int $handId, int $dealer): self
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('ts.*')
                ->from('table_seats', 'ts')
                ->leftJoin('ts', 'player_actions', 'pa', 'ts.id = pa.table_seat_id')
                ->where('pa.hand_id = ' . $queryBuilder->createNamedParameter($handId))
                ->andWhere('ts.id > ' . $queryBuilder->createNamedParameter($dealer))
                ->andWhere('pa.active = 1')
                ->setMaxResults(1);

            $rows = $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];

            $this->content = $rows;
            $this->setModelProperties($rows);

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function bigBlindWins(int $handId): bool
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
            /** @todo Might need subquery here for sing a join with update */
            // $queryBuilder = $this->connection->createQueryBuilder();
            // $queryBuilder
            //     ->update('table_seats', 'ts')
            //     ->leftJoin('ts', 'player_actions', 'pa', 'ts.id = pa.table_seat_id')
            //     ->set('ts.can_continue', 1)
            //     ->where('pa.hand_id = ' . $queryBuilder->createNamedParameter($handId))
            //     ->andWhere('pa.active = 1')
            //     ->andWhere('pa.big_blind = 1');

            // return $queryBuilder->executeStatement();
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->executeQuery();

            return true;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getContinuingPlayerSeats(string $handId): self
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('ts.*')
                ->from('player_actions', 'pa')
                ->leftJoin('pa', 'table_seats', 'ts', 'pa.table_seat_id = ts.id')
                ->where('pa.hand_id = ' . $queryBuilder->createNamedParameter($handId))
                ->andWhere('pa.active = 1')
                ->andWhere('ts.can_continue = 1');

            $rows = $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];

            $this->content = $rows;
            $this->setModelProperties($rows);

            return $this;
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getContinuingBetters(string $handId): array
    {
        $raiseId = Action::RAISE_ID;
        $betId   = Action::BET_ID;
        $callId  = Action::CALL_ID;

        try {
            $queryBuilder      = $this->connection->createQueryBuilder();
            $expressionBuilder = $this->connection->createExpressionBuilder();

            $queryBuilder
                ->select('ts.*')
                ->from('player_actions', 'pa')
                ->leftJoin('pa', 'table_seats', 'ts', 'pa.table_seat_id = ts.id')
                ->where('pa.hand_id = ' . $queryBuilder->createNamedParameter($handId))
                ->andWhere('ts.can_continue = 1')
                ->andWhere(
                    $expressionBuilder->in(
                        'pa.action_id',
                        [
                            $queryBuilder->createNamedParameter($raiseId),
                            $queryBuilder->createNamedParameter($betId),
                            $queryBuilder->createNamedParameter($callId),
                        ]
                    )
                );

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
