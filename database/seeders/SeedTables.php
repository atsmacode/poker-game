<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;

class SeedTables
{

    public static array $methods = [
        'seed'
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function seed()
    {
        $this->createTable()->createTableSeats();
    }

    private function createTable()
    {
        $name = 'Table 1';
        $seats = 6;

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO tables (name, seats) VALUES (:name, :seats)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':seats', $seats);

            $stmt->execute();

            $this->output->writeln("Table seeded successfully");
        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;

        return $this;

    }

    private function createTableSeats()
    {
        $seats = 6;

        try {
            $conn = new CustomPDO(true);

            $inserted = 0;

            while($inserted < $seats){

                $stmt = $conn->prepare("
                        INSERT INTO table_seats (table_id) VALUES (1)
                    ");
                $stmt->execute();

                $inserted++;
            }

            $this->output->writeln("Table seats seeded successfully");
        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;

        return $this;

    }


}