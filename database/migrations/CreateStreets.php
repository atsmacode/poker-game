<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateStreets extends Database
{

    public static array $methods = [
        'createStreetsTable',
    ];

    public function createStreetsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Streets table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

}