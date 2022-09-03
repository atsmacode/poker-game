<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateHandTypes extends Database
{

    public static array $methods = [
        'createHandTypesTable',
    ];

    public function createHandTypesTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE hand_types (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NULL
            )";

        try {
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Hand types table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

}