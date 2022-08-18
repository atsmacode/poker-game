<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateCards extends Database
{

    public static array $methods = [
        'createRanksTable',
        'createSuitsTable',
        'createCardsTable'
    ];

    public function createRanksTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE ranks (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NOT NULL,
                abbreviation VARCHAR(30) NOT NULL
            )";

        try {
            $this->connection->exec($sql);
            if ($showMessages) {
                $output->writeln("Ranks table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

    public function createSuitsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE suits (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
            abbreviation VARCHAR(30) NOT NULL
        )";

        try {
            $this->connection->exec($sql);
            if ($showMessages) {
                $output->writeln("Suits table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $this->connection = null;
    }

    public function createCardsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE cards (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            rank_id INT(6) UNSIGNED NOT NULL,
            suit_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (rank_id) REFERENCES ranks(id),
            FOREIGN KEY (suit_id) REFERENCES suits(id)
        )";

        try {
            $this->connection->exec($sql);
            if ($showMessages) {
                $output->writeln("Cards table created successfully");
            }
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }

        $this->connection = null;
    }

}