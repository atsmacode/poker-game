<?php

namespace App\Classes;

trait Connect
{
    public $servername;
    public $username;
    public $password;
    public $database;

    public function setCredentials()
    {
        [
            'servername' => $this->servername,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database
        ] = require('config/db.php');
    }

}