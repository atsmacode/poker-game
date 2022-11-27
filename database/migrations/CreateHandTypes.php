<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\PokerGame\Classes\Database;

class CreateHandTypes extends Database
{
    public static array $methods = [
        'createHandTypesTable',
    ];

    public function createHandTypesTable()
    {
        $sql = "CREATE TABLE hand_types (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NULL
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
