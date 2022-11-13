<?php

namespace Database\Seeders;

use App\Classes\Database;
use App\Constants\HandType;

class SeedHandTypes extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO hand_types (name, ranking) VALUES (:name, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':ranking', $ranking);

            foreach(HandType::ALL as $handType) {
                $name = $handType['name'];
                $ranking = $handType['ranking'];
                $stmt->execute();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());

        }
        
        $this->connection = null;
    }
}
