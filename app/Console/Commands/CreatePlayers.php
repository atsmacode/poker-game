<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use App\Classes\CustomPDO;
use PDOException;

class CreatePlayers
{

    use Connect;

    public static array $methods = [
        'createPlayersTable',
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function createPlayersTable($output)
    {

        $sql = "CREATE TABLE players (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(30) NOT NULL,
                email VARCHAR(30) NOT NULL,
                ai BOOLEAN DEFAULT 0
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Players table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}