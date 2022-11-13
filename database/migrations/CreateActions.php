<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateActions extends Database
{
    public static array $methods = [
        'createActionsTable',
    ];

    public function createActionsTable()
    {
        $sql = "CREATE TABLE actions (
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
