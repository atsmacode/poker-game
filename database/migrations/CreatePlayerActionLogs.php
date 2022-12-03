<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\Framework\Dbal\Database;

class CreatePlayerActionLogs extends Database
{
    public static array $methods = [
        'createPlayerActionLogsTable',
    ];

    public function createPlayerActionLogsTable()
    {
        $sql = "CREATE TABLE player_action_logs (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            player_status_id INT(6) UNSIGNED NOT NULL,
            bet_amount INT(6) UNSIGNED NULL,
            big_blind BOOLEAN DEFAULT 0,
            small_blind BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NOT NULL,
            action_id INT(6) UNSIGNED NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            hand_street_id INT(6) UNSIGNED NOT NULL,
            table_seat_id INT(6) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            FOREIGN KEY (player_status_id) REFERENCES player_actions(id),
            FOREIGN KEY (action_id) REFERENCES actions(id),
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            FOREIGN KEY (hand_street_id) REFERENCES hand_streets(id),
            FOREIGN KEY (table_seat_id) REFERENCES table_seats(id),
            INDEX (player_id),
            INDEX (hand_id),
            INDEX (hand_street_id),
            INDEX (table_seat_id),
            INDEX (player_status_id)
        )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
