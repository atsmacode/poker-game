<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateHandTypes
{

    public static array $methods = [
        'createHandTypesTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createHandTypesTable()
    {

        $sql = "CREATE TABLE hand_types (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Hand types table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}