<?php

namespace Database\Migrations;

use App\Models\Collection;
use App\Traits\Connect;
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

    public function dropDatabase($output, $showMessages = true)
    {

        $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";

        try {
            $this->connection = new PDO("mysql:host=$this->servername;", $this->username, $this->password);
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Database dropped successfully");
            }
        } catch(PDOException $e) {
            echo $sql . $e->getMessage();
        }

        return $this;

    }

    public function createDatabase($output, $showMessages = true)
    {

        $sql = "CREATE DATABASE `read-right-hands-vanilla`";

        try {
            $this->connection = new PDO("mysql:host=$this->servername;", $this->username, $this->password);
            $this->connection->exec($sql);

            if ($showMessages) {
                $output->writeln("Database created successfully");
            }
        } catch(PDOException $e) {
            $this->output->writeln($sql . "<br>" . $e->getMessage());
        }

        return $this;

    }

}