<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use PDO;
use PDOException;

class CreateTables
{

    use Connect;

    public static array $methods = [
        'createTablesTable',
        'createTableSeatsTable'
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function createTablesTable($output)
    {

        $sql = "CREATE TABLE tables (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                seats INT(2) NOT NULL
            )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Tables table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    public function createTableSeatsTable($output)
    {

        $sql = "CREATE TABLE table_seats (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            number INT(6) UNSIGNED NULL,
            can_continue BOOLEAN DEFAULT 0,
            is_dealer BOOLEAN DEFAULT 0,
            action_on BOOLEAN DEFAULT 0,
            player_id INT(6) UNSIGNED NOT NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (table_id) REFERENCES tables(id)
        )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Table seats table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }


}