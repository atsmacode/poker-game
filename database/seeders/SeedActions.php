<?php

namespace Atsmacode\PokerGame\Database\Seeders;

use Atsmacode\Framework\Database\Database;
use Atsmacode\PokerGame\Constants\Action;

class SeedActions extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        try {
            foreach(Action::ALL as $action) {
                $name = $action['name'];
                $stmt = $this->connection->prepare("INSERT INTO actions (name) VALUES (:name)");
                $stmt->bindParam(':name', $name);
                $stmt->execute();
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
