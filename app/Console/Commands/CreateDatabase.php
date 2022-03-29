<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use PDO;
use PDOException;

class CreateDatabase
{

    use Connect;

    public static array $methods = [
        'dropDatabase',
        'createDatabase'
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function dropDatabase($output)
    {

        $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";

        try {
            $conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
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
            $conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Database created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;

        return $this;

    }

}