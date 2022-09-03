<?php

namespace Database\Migrations;

use App\Classes\Database;

class CreateActions extends Database
{

    public static array $methods = [
        'createActionsTable',
    ];

    public function createActionsTable($output, $showMessages = true)
    {

        $sql = "CREATE TABLE actions (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $this->connection->exec($sql);

            if($showMessages){
                $output->writeln("Actions table created successfully");
            }
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }

        $this->connection = null;
    }

}