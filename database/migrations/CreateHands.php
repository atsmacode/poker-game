<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateHands extends Database
{

    public static array $methods = [
        'createHandsTable',
        'createHandStreetsTable',
        'createHandStreetCardsTable'
    ];

    public function createHandsTable($output)
    {

        $sql = "CREATE TABLE hands (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                game_type_id int(6) NULL,
                table_id INT(6) UNSIGNED NULL,
                completed_on DATETIME NULL,
                FOREIGN KEY (table_id) REFERENCES tables(id)
            )";

        try {
            $this->connection->exec($sql);
            $output->writeln("Hands table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

    public function createHandStreetsTable($output)
    {

        $sql = "CREATE TABLE hand_streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                street_id int(6) UNSIGNED NOT NULL,
                hand_id INT(6) UNSIGNED NOT NULL,
                FOREIGN KEY (hand_id) REFERENCES hands(id),
                FOREIGN KEY (street_id) REFERENCES streets(id)
            )";

        try {
            $this->connection->exec($sql);
            $output->writeln("Hand streets table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

    public function createHandStreetCardsTable($output)
    {

        $sql = "CREATE TABLE hand_street_cards (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                hand_street_id INT(6) UNSIGNED NOT NULL,
                card_id INT(6) UNSIGNED NOT NULL,
                FOREIGN KEY (hand_street_id) REFERENCES hand_streets(id),
                FOREIGN KEY (card_id) REFERENCES cards(id)
            )";

        try {
            $this->connection->exec($sql);
            $output->writeln("Hand street cards table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

}