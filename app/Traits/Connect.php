<?php

namespace App\Traits;

trait Connect
{
    public $servername;
    public $username;
    public $password;
    public $database;

    public function setCredentials()
    {
        $dbConfig = 'db.php';
        if (isset($GLOBALS['dev'])) {
            $dbConfig = 'db-test.php';
        }

        [
            'servername' => $this->servername,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database
        ] = require('config/' . $dbConfig);
    }
}