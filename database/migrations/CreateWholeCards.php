<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateWholeCards
{

    public static array $methods = [
        'createWholeCardsTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createWholeCardsTable()
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
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Whole cards table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }

}