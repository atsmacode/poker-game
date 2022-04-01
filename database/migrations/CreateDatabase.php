<?php

namespace Database\Migrations;

use App\Classes\CustomPDO;
use PDOException;

class CreateDatabase
{

    public static array $methods = [
        'dropDatabase',
        'createDatabase'
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function dropDatabase()
    {

        $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";

        try {
            $conn = new CustomPDO();
            $conn->exec($sql);
            $this->output->writeln("Database dropped successfully");
        } catch(PDOException $e) {
            echo $sql . $e->getMessage();
        }

        $conn = null;

        return $this;

    }

    public function createDatabase()
    {

        $sql = "CREATE DATABASE `read-right-hands-vanilla`";

        try {
            $conn = new CustomPDO();
            $conn->exec($sql);
            $this->output->writeln("Database created successfully");
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;

        return $this;

    }

}