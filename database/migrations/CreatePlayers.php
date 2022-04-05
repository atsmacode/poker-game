<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreatePlayers extends Database
{

    public static array $methods = [
        'createPlayersTable',
    ];

    public function createPlayersTable($output)
    {

        $sql = "CREATE TABLE players (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(30) NOT NULL,
                email VARCHAR(30) NOT NULL,
                ai BOOLEAN DEFAULT 0
            )";

        try {
            $this->connection->exec($sql);
            $output->writeln("Players table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

}