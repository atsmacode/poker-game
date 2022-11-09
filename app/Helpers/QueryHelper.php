<?php

namespace App\Helpers;

use App\Classes\Database;
use PDOException;

class QueryHelper
{
    public static function selectRanks()
    {
        $rows = null;

        try {
            $db = new Database();
            $stmt = $db->connection->prepare("SELECT * FROM ranks ORDER BY ranking DESC");
            $stmt->execute();

            $rows = $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        $db = null;

        return $rows;
    }

    public static function selectSuits()
    {
        $rows = null;

        try {
            $db = new Database();
            $stmt = $db->connection->prepare("SELECT * FROM suits");
            $stmt->execute();

            $rows = $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        $db = null;

        return $rows;
    }
}
