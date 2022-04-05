<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreatePots extends Database
{

    public static array $methods = [
        'createPotsTable',
    ];

    public function createPotsTable($output)
    {

        $sql = "CREATE TABLE pots (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount INT(12) UNSIGNED NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (hand_id) REFERENCES hands(id)
        )";

        try {
            $this->connection->exec($sql);
            $output->writeln("Pots table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }


}