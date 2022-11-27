<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\Orm\Classes\Database;

class CreatePots extends Database
{
    public static array $methods = [
        'createPotsTable',
    ];

    public function createPotsTable()
    {
        $sql = "CREATE TABLE pots (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount INT(12) UNSIGNED NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            INDEX (hand_id)
        )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
