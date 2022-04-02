<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateStacks
{

    public static array $methods = [
        'createStacksTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createStacksTable()
    {

        $sql = "CREATE TABLE stacks (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amount INT(12) UNSIGNED NULL,
            player_id INT(6) UNSIGNED NOT NULL,
            table_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (player_id) REFERENCES players(id),
            FOREIGN KEY (table_id) REFERENCES tables(id)
        )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Stacks table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }


}