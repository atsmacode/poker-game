<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateActions
{

    public static array $methods = [
        'createActionsTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createActionsTable()
    {

        $sql = "CREATE TABLE actions (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Actions table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }

}