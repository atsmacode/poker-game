<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateWholeCards extends Database
{

    public static array $methods = [
        'createWholeCardsTable',
    ];

    public function createWholeCardsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE whole_cards (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            card_id INT(6) UNSIGNED NOT NULL,
            hand_id INT(6) UNSIGNED NULL,
            player_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (card_id) REFERENCES cards(id),
            FOREIGN KEY (hand_id) REFERENCES hands(id),
            FOREIGN KEY (player_id) REFERENCES players(id)
        )";

        try {
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Whole cards table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }

        $this->connection = null;
    }

}