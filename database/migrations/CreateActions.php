<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateActions
{

    public static array $methods = [
        'createActionsTable',
    ];

    public function createActionsTable($output)
    {

        $sql = "CREATE TABLE actions (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $output->writeln("Actions table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }

}