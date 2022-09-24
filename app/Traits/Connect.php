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
        /**
         * Manually switching between DIR references.
         * They are different when actually hitting
         * endpoint and running unit tests: TODO
         */
        $dbConfig = 'config/db.php';

        if (isset($GLOBALS['dev'])) {
            $dbConfig = 'config/db-test.php';
        }

        [
            'servername' => $this->servername,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database
        ] = require($GLOBALS['THE_ROOT'] . $dbConfig);
    }
}
