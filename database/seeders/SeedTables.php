<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedTables extends Database
{

    public static array $methods = [
        'seed'
    ];

    public function seed($output, $showMessages = true)
    {
        $this->createTable($output, $showMessages)->createTableSeats($output, $showMessages);
    }

    private function createTable($output, $showMessages = true)
    {
        $name = 'Table 1';
        $seats = 6;

        try {
            $stmt = $this->connection->prepare("INSERT INTO tables (name, seats) VALUES (:name, :seats)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':seats', $seats);

            $stmt->execute();

            if ($showMessages) {
                $output->writeln("Table seeded successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }

        return $this;
    }

    private function createTableSeats($output, $showMessages = true)
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

            if ($showMessages) {
                $output->writeln("Table seats seeded successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }
        $this->connection = null;

        return $this;
    }
}