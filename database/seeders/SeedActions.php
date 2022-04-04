<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;

class SeedActions
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

        $actions = require('config/actions.php');

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO actions (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            foreach($actions as $action) {
                $name = $action['name'];
                $stmt->execute();
            }
            $this->output->writeln("Actions seeded successfully");
        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;
    }

}