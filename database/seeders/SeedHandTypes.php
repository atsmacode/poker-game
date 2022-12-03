<?php

namespace Atsmacode\PokerGame\Database\Seeders;

use Atsmacode\Framework\Dbal\Database;
use Atsmacode\PokerGame\Constants\HandType;

class SeedHandTypes extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        try {
            foreach(HandType::ALL as $handType) {
                $name    = $handType['name'];
                $ranking = $handType['ranking'];
                $stmt    = $this->connection->prepare("INSERT INTO hand_types (name, ranking) VALUES (:name, :ranking)");
                
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':ranking', $ranking);
                $stmt->execute();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());

        }
        
        $this->connection = null;
    }
}
