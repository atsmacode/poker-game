<?php

namespace App\Console\Commands;

use App\Classes\CustomPDO;
use PDOException;

class CreateHandTypes
{

    public static array $methods = [
        'createHandTypesTable',
    ];

    public function createHandTypesTable($output)
    {

        $sql = "CREATE TABLE hand_types (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Hand types table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}