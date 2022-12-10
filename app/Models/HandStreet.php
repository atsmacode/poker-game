<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class HandStreet extends Model
{
    use Collection;

    protected $table = 'hand_streets';
    public $id;

    public function cards()
    {
       $query = sprintf("
            SELECT
                *
            FROM
                hand_street_cards
            WHERE
                hand_street_id = :hand_street_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_street_id', $this->id);

            $results = $stmt->executeQuery();
            
            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getStreetCards($handId, $streetId)
    {
        $query = sprintf("
            SELECT
                *
            FROM
                hand_streets AS hs
            LEFT JOIN
                hand_street_cards AS hsc ON hs.id = hsc.hand_street_id
            WHERE
                hs.hand_id = :hand_id
            AND
                street_id  = :street_id
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':street_id', $streetId);

            $results = $stmt->executeQuery();
            
            return $results->fetchAllAssociative();
        } catch(\Exception $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }
}
