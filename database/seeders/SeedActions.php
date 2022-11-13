<?php

namespace Database\Seeders;

use App\Classes\Database;
use App\Constants\Action;

class SeedActions extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO actions (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach(Action::ALL as $action) {
                $name = $action['name'];
                $stmt->execute();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
