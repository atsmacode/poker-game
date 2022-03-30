<?php

namespace App\Helpers;

use App\Classes\CustomPDO;
use PDOException;

class QueryHelper
{

    public static function selectRanks($output = null)
    {

        $rows = null;

        try {

            $conn = new CustomPDO(true);
            $stmt = $conn->prepare("SELECT * FROM ranks");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            if($output){
                $output->writeln("Ranks selected successfully");
            } else {
                echo "Ranks selected successfully";
            }

        } catch(PDOException $e) {
            if($output){
                $output->writeln($e->getMessage());
            } else {
                echo $e->getMessage();
            }
        }

        $conn = null;

        return $rows;

    }

    public static function selectSuits($output = null)
    {

        $rows = null;

        try {

            $conn = new CustomPDO(true);
            $stmt = $conn->prepare("SELECT * FROM suits");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            if($output){
                $output->writeln("Suits selected successfully");
            }

        } catch(PDOException $e) {
            if($output){
                $output->writeln($e->getMessage());
            } else {
                echo $e->getMessage();
            }
        }
        $conn = null;

        return $rows;
    }

}