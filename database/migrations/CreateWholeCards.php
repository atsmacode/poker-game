<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateWholeCards extends Database
{
    public static array $methods = [
        'createWholeCardsTable',
    ];

    public function createWholeCardsTable()
    {
        $sql = "CREATE TABLE whole_cards (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            card_id INT(6) UNSIGNED NOT NULL,
            hand_id INT(6) UNSIGNED NULL,
            player_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (card_id) REFERENCES cards(id),
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            FOREIGN KEY (player_id) REFERENCES players(id),
            INDEX (hand_id),
            INDEX (player_id)
        )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
