<?php

namespace App\Models;

use PDO;
use PDOException;

class HandStreet extends Model
{

    use Collection;

    protected $table = 'hand_streets';
    public $id;

    public function cards()
    {
        return HandStreetCard::find(['hand_street_id' => $this->id]);
    }

    public static function getStreetCards($handId, $streetId)
    {
        return (new static())->getStreetCardsQuery($handId, $streetId);
    }

    private function getStreetCardsQuery($handId, $streetId)
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
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->bindParam(':street_id', $streetId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
