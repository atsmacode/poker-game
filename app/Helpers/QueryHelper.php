<?php

namespace App\Helpers;

use PDO;
use PDOException;

class QueryHelper
{

    public static function selectRanks($output)
    {

        [
            'servername' => $servername,
            'username' => $username,
            'password' => $password,
            'database' => $database
        ] = require('config/db.php');

        $rows = null;

        try {

            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM ranks");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $output->writeln("Ranks selected successfully");

        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }

        $conn = null;

        return $rows;

    }

    public static function selectSuits($output)
    {

        [
            'servername' => $servername,
            'username' => $username,
            'password' => $password,
            'database' => $database
        ] = require('config/db.php');

        $rows = null;

        try {

            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM suits");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $output->writeln("Suits selected successfully");

        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }
        $conn = null;

        return $rows;
    }

}