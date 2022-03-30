<?php

namespace App\Classes;

use PDO;

class CustomPDO extends PDO
{

    use Connect;

    public function __construct($database = false)
    {
        $this->setCredentials();

        $database = $database ? $this->database : '';

        parent::__construct("mysql:host=$this->servername;dbname=$database", $this->username, $this->password);

        self::setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
    }
}