<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedTables extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        $this->createTable()->createTableSeats();
    }

    private function createTable()
    {
        /**
         * TODO: only supporting 1 table for the time being.
         */
        $name  = 'Table 1';
        $seats = 6;

        try {
            $stmt = $this->connection->prepare("INSERT INTO tables (name, seats) VALUES (:name, :seats)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':seats', $seats);

            $stmt->execute();
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        return $this;
    }

    private function createTableSeats()
    {
        $seats = 6;

        try {
            $inserted = 0;

            while($inserted < $seats){

                $stmt = $this->connection->prepare("
                        INSERT INTO table_seats (table_id) VALUES (1)
                    ");
                $stmt->execute();

                $inserted++;
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());;
        }

        $this->connection = null;

        return $this;
    }
}
