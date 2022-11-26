<?php

namespace App\Classes;

use App\Traits\Connect;
use PDO;

class Database
{
    use Connect;

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
