<?php

namespace App\Classes;

use App\Traits\Connect;
use PDO;

class Database
{
    use Connect;

    /**
     * PDO class can't be serialized normally.
     * 
     * Using magic methods to make this possible.
     *
     * @return array
     */
    public function __serialize(): array
    {
        $this->connection = (array) $this->connection;
        
        return (array) $this;
    }

    public function __unserialize(array $data): void
    {
        $this->connection = $data['connection'];
        $this->servername = $data['servername'];
        $this->database   = $data['database'];
        $this->username   = $data['username'];
        $this->password   = $data['password'];
    }

    public function __construct()
    {
        $this->setCredentials();

        /*
         * https://www.php.net/manual/en/pdo.connections.php
         * PDO::ATTR_PERSISTENT must be set to use persistent connections.
         * If unset, test suite will fail with too many connections.
         */
        $this->connection = new PDO(
            "mysql:host=$this->servername;dbname=$this->database",
            $this->username,
            $this->password,
            array(
            PDO::ATTR_PERSISTENT => true
        ));

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
