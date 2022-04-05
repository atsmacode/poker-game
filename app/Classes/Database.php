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

        $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function __destruct()
    {
        $this->connection = null;
    }
}