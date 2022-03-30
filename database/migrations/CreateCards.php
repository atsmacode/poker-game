<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateCards
{

    public static array $methods = [
        'createRanksTable',
        'createSuitsTable',
        'createCardsTable'
    ];

    public function createRanksTable($output)
    {

        $sql = "CREATE TABLE ranks (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NOT NULL,
                abbreviation VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Ranks table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    public function createSuitsTable($output)
    {

        $sql = "CREATE TABLE suits (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
            abbreviation VARCHAR(30) NOT NULL
        )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Suits table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    public function createCardsTable($output)
    {

        $sql = "CREATE TABLE cards (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            rank_id INT(6) UNSIGNED NOT NULL,
            suit_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (rank_id) REFERENCES ranks(id),
            FOREIGN KEY (suit_id) REFERENCES suits(id)
        )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Cards table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }

}