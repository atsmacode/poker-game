<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateTables
{

    public static array $methods = [
        'createTablesTable',
        'createTableSeatsTable'
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createTablesTable()
    {

        $sql = "CREATE TABLE tables (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                seats INT(2) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Tables table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    public function createTableSeatsTable()
    {

        $sql = "CREATE TABLE table_seats (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            number INT(6) UNSIGNED NULL,
            can_continue BOOLEAN DEFAULT 0,
            is_dealer BOOLEAN DEFAULT 0,
            action_on BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            updated_at DATETIME NULL,
            FOREIGN KEY (table_id) REFERENCES tables(id),
            FOREIGN KEY (player_id) REFERENCES players(id)
        )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Table seats table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }


}