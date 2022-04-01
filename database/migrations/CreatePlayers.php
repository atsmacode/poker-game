<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreatePlayers
{

    public static array $methods = [
        'createPlayersTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createPlayersTable()
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
            $this->output->writeln("Players table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}