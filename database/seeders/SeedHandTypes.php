<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedHandTypes extends Database
{

    public static array $methods = [
        'seed'
    ];

    public function seed($output)
    {

        $handTypes = require('config/handtypes.php');

        try {
            $stmt = $this->connection->prepare("INSERT INTO hand_types (name, ranking) VALUES (:name, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':ranking', $ranking);

            foreach($handTypes as $handType) {
                $name = $handType['name'];
                $ranking = $handType['ranking'];
                $stmt->execute();
            }
            $output->writeln("Hand types seeded successfully");
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());

        }
        $conn = null;
    }

}