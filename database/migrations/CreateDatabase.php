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

    public function dropDatabase($output)
    {

        $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";

        try {
            $conn = new CustomPDO();
            $conn->exec($sql);
            $output->writeln("Database dropped successfully");
        } catch(PDOException $e) {
            echo $sql . $e->getMessage();
        }

        $conn = null;

        return $this;

    }

    public function createDatabase($output)
    {

        $sql = "CREATE DATABASE `read-right-hands-vanilla`";

        try {
            $conn = new CustomPDO();
            $conn->exec($sql);
            $output->writeln("Database created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;

        return $this;

    }

}