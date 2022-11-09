<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedActions extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        $actions = require('config/actions.php');

        try {
            $stmt = $this->connection->prepare("INSERT INTO actions (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach($actions as $action) {
                $name = $action['name'];
                $stmt->execute();
            }
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
