<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Player extends Model
{
    use Collection;

    protected     $table = 'players';
    public int    $id;
    public string $name;
    public string $email;  

    public function stacks(): array
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('stacks')
                ->where('player_id = ' . $queryBuilder->createNamedParameter($this->id));

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getWholeCards(int $handId, int $playerId): array
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select(
                    'c.*',
                    'wc.player_id',
                    'r.name rank',
                    'r.abbreviation rankAbbreviation',
                    's.name suit',
                    's.abbreviation suitAbbreviation',
                    'r.ranking ranking '
                )
                ->from('whole_cards', 'wc')
                ->leftJoin('wc', 'cards', 'c', 'wc.card_id = c.id')
                ->leftJoin('c', 'ranks', 'r', 'c.rank_id = r.id')
                ->leftJoin('c', 'suits', 's', 'c.suit_id = s.id')
                ->where('wc.hand_id = ' . $queryBuilder->createNamedParameter($handId))
                ->andWhere('wc.player_id = ' . $queryBuilder->createNamedParameter($playerId));

            return $queryBuilder->executeStatement() ? $queryBuilder->fetchAllAssociative() : [];
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
