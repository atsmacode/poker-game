<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\PokerGame\Classes\Database;

class CreateStreets extends Database
{
    public static array $methods = [
        'createStreetsTable',
    ];

    public function createStreetsTable()
    {
        $sql = "CREATE TABLE streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
