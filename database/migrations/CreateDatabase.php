<?php

namespace Database\Migrations;

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

    public function dropDatabase()
    {
        $sql = "DROP DATABASE IF EXISTS `{$this->database}`";

        try {
            $this->connection = new PDO("mysql:host=$this->servername;", $this->username, $this->password);
            $this->connection->exec($sql);
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        return $this;
    }

    public function createDatabase()
    {
        $sql = "CREATE DATABASE `{$this->database}`";

        try {
            $this->connection = new PDO("mysql:host=$this->servername;", $this->username, $this->password);
            $this->connection->exec($sql);
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        return $this;
    }
}
