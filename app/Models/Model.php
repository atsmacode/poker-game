<?php

namespace App\Models;

use App\Classes\CustomPDO;
use PDO;
use PDOException;

class Model
{

    protected $table;
    protected $selected;
    public $content;

    protected function findOrCreate($column = null)
    {
        if($this->selected && !$this->getSelected($column, $this->selected)){
            $this->create($column, $this->selected);
        };
    }

    protected function create($column = null, $value = null)
    {

        $id = null;

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO {$this->table} ($column) VALUES (:value)");
            $stmt->bindParam(':value', $value);
            $stmt->execute();

            $id = $conn->lastInsertId();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;

        $this->content = $this->getSelected('id', $id)->content;

    }

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

        if(!$rows){
            return null;
        }

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