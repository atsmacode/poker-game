<?php

namespace App\Models;

use App\Classes\CustomPDO;
use PDO;
use PDOException;

class Model
{

    public $table;
    public array $content;

    protected function getSelected($column = null, $value = null)
    {
        $rows = null;

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT * FROM {$this->table}
                    WHERE {$column} = '{$value}'
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $conn = null;

        $result = array_shift($rows);
        $this->content = $result;

        $this->{$column} = $result[$column];

        return $this;

    }

    public function all()
    {
        $rows = null;

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT * FROM {$this->table}
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $conn = null;

        $result = $rows;
        $this->content = $result;

        return $this;

    }

}