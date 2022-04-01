<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;

class SeedHandTypes
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

        $handTypes = require('config/handtypes.php');

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO hand_types (name, ranking) VALUES (:name, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':ranking', $ranking);

            foreach($handTypes as $handType) {
                $name = $handType['name'];
                $ranking = $handType['ranking'];
                $stmt->execute();
            }
            $this->output->writeln("Hand types seeded successfully");
        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;
    }

}