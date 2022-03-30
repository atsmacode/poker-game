<?php

namespace App\Helpers;

use PDO;
use PDOException;

class QueryHelper
{

    public static function selectRanks($servername, $username, $password, $database, $output)
    {

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

    public static function selectSuits($servername, $username, $password, $database, $output)
    {

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