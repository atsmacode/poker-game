<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use App\Helpers\QueryHelper;
use PDO;
use PDOException;

class SeedHandTypes
{

    use Connect;

    public static array $methods = [
        'seed'
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function seed($output)
    {

        $handTypes = require('config/handtypes.php');

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO hand_types (name, ranking) VALUES (:name, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':ranking', $ranking);

            foreach($handTypes as $handType) {
                $name = $handType['name'];
                $ranking = $handType['ranking'];
                $stmt->execute();
            }
            $output->writeln("Hand types seeded successfully");
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());

        }
        $conn = null;
    }

}