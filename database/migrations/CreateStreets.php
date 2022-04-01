<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;

class CreateStreets
{

    public static array $methods = [
        'createStreetsTable',
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function createStreetsTable()
    {

        $sql = "CREATE TABLE streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new CustomPDO(true);
            $conn->exec($sql);
            $this->output->writeln("Streets table created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}