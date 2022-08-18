<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedStreets extends Database
{

    public static array $methods = [
        'seed'
    ];

    public function seed($output, $showMessages = true)
    {

        $streets = require('config/streets.php');

        try {

            $stmt = $this->connection->prepare("INSERT INTO streets (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach($streets as $street) {
                $name = $street['name'];
                $stmt->execute();
            }

            if ($showMessages) {
                $output->writeln("Streets seeded successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());

        }
        $conn = null;
    }

}