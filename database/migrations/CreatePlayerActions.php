<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreatePlayerActions extends Database
{

    public static array $methods = [
        'createPlayerActionsTable',
    ];

    public function createPlayerActionsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE player_actions (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            bet_amount INT(6) UNSIGNED NULL,
            active BOOLEAN DEFAULT 0,
            big_blind BOOLEAN DEFAULT 0,
            small_blind BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NOT NULL,
            action_id INT(6) UNSIGNED NULL,
            hand_id INT(6) UNSIGNED NOT NULL,
            hand_street_id INT(6) UNSIGNED NOT NULL,
            table_seat_id INT(6) UNSIGNED NOT NULL,
            updated_at DATETIME NULL,
            FOREIGN KEY (action_id) REFERENCES actions(id),
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            FOREIGN KEY (hand_street_id) REFERENCES hand_streets(id),
            FOREIGN KEY (table_seat_id) REFERENCES table_seats(id)
        )";

        try {
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Player actions table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }


}