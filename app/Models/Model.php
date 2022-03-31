<?php

namespace App\Models;

use App\Classes\CustomPDO;
use PDO;
use PDOException;

class Model
{

    protected $table;
    public $content;
    public $data;

    private function compileWhereStatement($data)
    {
        $properties = "WHERE ";

        foreach($data as $column => $value){
            $properties .= $column . " = '". $value . "'";

            if(next($data)){
                $properties .= " AND ";
            };
        }

        return $properties;
    }

    private function compileInsertStatement($data)
    {
        $properties = "INSERT INTO {$this->table} (";

        foreach(array_keys($data) as $column){
            $properties .= $column;

            if(next($data)){
                $properties .= ", ";
            } else {
                $properties .= ") ";
            };
        }

        $properties .= "VALUES (";

        reset($data);

        foreach(array_keys($data) as $column){
            $properties .= ':'.$column;

            if(next($data)){
                $properties .= ", ";
            } else {
                $properties .= ")";
            };
        }

        return $properties;
    }

    private function setModelProperties($result)
    {
        foreach($result as $column => $value){
            $this->{$column} = $value;
        }
    }

    protected function findOrCreate($data = null)
    {
        if($this->data && !$this->getSelected($data)){
            $this->create($data);
        };
    }

    protected function create($data)
    {

        $id = null;

        $insertStatement = $this->compileInsertStatement($data);

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare($insertStatement);

            /*
             * https://stackoverflow.com/questions/27978175/pdo-bindparam-php-foreach-loop
             * Link above suggested passing by reference is a must - not sure why,
             * need further research into the concept. It seemed the last $value
             * parameter was being set to all the columns without this: &$value
             */
            foreach($data as $column => &$value){
                $stmt->bindParam($column, $value);
            }

            $stmt->execute();

            $id = $conn->lastInsertId();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;

        $this->content = $this->getSelected(['id' => $id])->content;

    }

    protected function getSelected($data)
    {
        $rows = null;

        $properties = $this->compileWhereStatement($data);

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT * FROM {$this->table}
                    {$properties}
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

        $this->setModelProperties($result);

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