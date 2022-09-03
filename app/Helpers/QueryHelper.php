<?php

namespace App\Helpers;

use App\Classes\Database;
use PDOException;

class QueryHelper
{

    public static function selectRanks($output = null)
    {

        $rows = null;

        try {

            $db = new Database();
            $stmt = $db->connection->prepare("SELECT * FROM ranks");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            if($output){
                $output->writeln("Ranks selected successfully" . PHP_EOL);
            } else {
                echo "Ranks selected successfully"  . PHP_EOL;
            }

        } catch(PDOException $e) {
            if($output){
                $output->writeln($e->getMessage());
            } else {
                echo $e->getMessage();
            }
        }

        $db = null;

        return $rows;

    }

    public static function selectSuits($output = null)
    {

        $rows = null;

        try {

            $db = new Database();
            $stmt = $db->connection->prepare("SELECT * FROM suits");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            if($output){
                $output->writeln("Suits selected successfully"  . PHP_EOL);
            }

        } catch(PDOException $e) {
            if($output){
                $output->writeln($e->getMessage());
            } else {
                echo $e->getMessage();
            }
        }
        $db = null;

        return $rows;
    }

}