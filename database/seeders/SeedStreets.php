<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedStreets extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        $streets = require('config/streets.php');

        try {
            $stmt = $this->connection->prepare("INSERT INTO streets (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach($streets as $street) {
                $name = $street['name'];
                $stmt->execute();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
