<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreatePlayers extends Database
{
    public static array $methods = [
        'createPlayersTable',
    ];

    public function createPlayersTable()
    {
        $sql = "CREATE TABLE players (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                email VARCHAR(30) NOT NULL,
                ai BOOLEAN DEFAULT 0
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
