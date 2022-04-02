<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;

class SeedStreets
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

        $streets = require('config/streets.php');

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO streets (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach($streets as $street) {
                $name = $street['name'];
                $stmt->execute();
            }
            $this->output->writeln("Streets seeded successfully");
        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;
    }

}