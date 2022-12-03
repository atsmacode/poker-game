<?php

namespace Atsmacode\PokerGame\Database\Migrations;

use Atsmacode\Framework\Dbal\Database;

class CreateHands extends Database
{
    public static array $methods = [
        'createHandsTable',
        'createHandStreetsTable',
        'createHandStreetCardsTable'
    ];

    public function createHandsTable()
    {
        $sql = "CREATE TABLE hands (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                game_type_id int(6) NULL,
                table_id INT(6) UNSIGNED NULL,
                completed_on DATETIME NULL,
                FOREIGN KEY (table_id) REFERENCES tables(id),
                INDEX (table_id)
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }

    public function createHandStreetsTable()
    {
        $sql = "CREATE TABLE hand_streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                street_id int(6) UNSIGNED NOT NULL,
                hand_id INT(6) UNSIGNED NOT NULL,
                FOREIGN KEY (hand_id) REFERENCES hands(id),
                FOREIGN KEY (street_id) REFERENCES streets(id),
                INDEX (hand_id)
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }

    public function createHandStreetCardsTable()
    {
        $sql = "CREATE TABLE hand_street_cards (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                hand_street_id INT(6) UNSIGNED NOT NULL,
                card_id INT(6) UNSIGNED NOT NULL,
                FOREIGN KEY (hand_street_id) REFERENCES hand_streets(id),
                FOREIGN KEY (card_id) REFERENCES cards(id),
                INDEX (hand_street_id)
            )";

        try {
            $this->connection->exec($sql);
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
