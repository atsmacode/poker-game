<?php

namespace App\Console\Commands;

use App\Classes\Connect;
use PDO;
use PDOException;

class CreateStreets
{

    use Connect;

    public static array $methods = [
        'createStreetsTable',
    ];

    public function __construct()
    {
        $this->setCredentials();
    }

    public function createStreetsTable($output)
    {

        $sql = "CREATE TABLE streets (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Streets table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

}