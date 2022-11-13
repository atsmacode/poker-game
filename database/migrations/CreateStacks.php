<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateStacks extends Database
{
    public static array $methods = [
        'createStacksTable',
    ];

    public function createStacksTable()
    {
        /**
         * TODO amount is not unsigned to allow negative
         * values until 'player loses/zero-chips feature
         * is added.
         */
        $sql = "CREATE TABLE stacks (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount BIGINT(12) NULL,
            player_id INT(6) UNSIGNED NOT NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (table_id) REFERENCES tables(id),
            INDEX (player_id),
            INDEX (table_id)
        )";

        try {
            $this->connection->exec($sql);
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
