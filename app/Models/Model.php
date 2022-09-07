<?php

namespace App\Models;

use App\Classes\Database;
use PDO;
use PDOException;

class Model extends Database
{
    protected $table;
    public $content = [];
    public $data;

    public function __construct(array $data = null)
    {
        parent::__construct();
        $this->data = $data;
    }

    public static function find(array $data = null)
    {
        return (new static($data))->getSelected($data);
    }

    public static function create(array $data = null)
    {
        return (new static($data))->createEntry($data);
    }

    public function contains(array $data)
    {
        return in_array($data, $this->content);
    }

    public function createEntry($data)
    {

        $id = null;

        $insertStatement = $this->compileInsertStatement($data);

        try {

            $stmt = $this->connection->prepare($insertStatement);

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

            $id = $this->connection->lastInsertId();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $this->content = $this->getSelected(['id' => $id])->content;

        return $this;

    }

    protected function getSelected($data)
    {
        $rows = null;

        $properties = $this->compileWhereStatement($data);

        try {

            $stmt = $this->connection->prepare("
                    SELECT * FROM {$this->table}
                    {$properties}
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        if(!$rows){
            return $this;
        }

        $this->content = $rows;

        $this->setModelProperties($rows);

        return $this;
    }

    /**
     * To be used to update a single model instance.
     *
     * @param array $data
     * @return void
     */
    public function update($data)
    {
        $properties = $this->compileUpdateStatement($data);

        try {
            $stmt = $this->connection->prepare($properties);

            foreach($data as $column => &$value){
                if ($value !== null) { 
                    $stmt->bindParam(':'.$column, $value);
                }
            }

            // if ($this->table == 'player_actions' || $this->table == 'table_seats') {
            //     $now = date('Y-m-d H:i:s');
            //     $stmt->bindParam(':updated_at', $now);
            // }

            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $this->content = $this->getSelected(['id' => $this->id])->content;

        return $this;
    }

    /**
     * To be used to update a multiple model instances.
     *
     * @param array $data
     * @return void
     */
    public function updateBatch($data, $where = null)
    {
        $properties = $this->compileUpdateBatchStatement($data, $where);

        try {
            $stmt = $this->connection->prepare($properties);

            foreach($data as $column => &$value){
                if ($value !== null) { 
                    $stmt->bindParam(':'.$column, $value);
                }
            }
            
            // if ($this->table == 'player_actions' || $this->table == 'table_seats') {
            //     $now = date('Y-m-d H:i:s');
            //     $stmt->bindParam(':updated_at', $now);
            // }

            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return $this;
    }

    public function all()
    {
        $rows = null;

        try {

            $stmt = $this->connection->prepare("
                    SELECT * FROM {$this->table}
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $result = $rows;
        $this->content = $result;

        return $this;
    }

    private function compileUpdateStatement($data)
    {
        $properties = "UPDATE {$this->table} SET ";

        $pointer = 1;
        foreach($data as $column => $value){
            if($value !== null){
                $properties .= $column . " = :". $column;

                if($pointer < count($data)){
                    $properties .= ", ";
                };
            }
            $pointer++;
        }

        // if ($this->table == 'player_actions' || $this->table == 'table_seats') {
        //     $properties .= ', updated_at = :updated_at';
        // }
    
        $properties .= " WHERE id = {$this->id}";

        return $properties;
    }

    private function compileUpdateBatchStatement($data, $where = null)
    {
        $properties = "UPDATE {$this->table} SET ";

        $pointer = 1;
        foreach($data as $column => $value){
            if($value !== null){
                $properties .= $column . " = :". $column;

                if($pointer < count($data)){
                    $properties .= ", ";
                };
            }
            $pointer++;
        };

        // if ($this->table == 'player_actions' || $this->table == 'table_seats') {
        //     $properties .= ', updated_at = :updated_at';
        // }
        if ($where) {
            $properties .= " WHERE {$where}";
        }

        return $properties;
    }

    private function compileWhereIdStatement($data)
    {
        $properties = "WHERE ";

        $properties .= " id = '". $data['id'] . "'";

        return $properties;
    }

    private function compileWhereStatement($data)
    {
        $properties = "WHERE ";

        $pointer = 1;
        foreach($data as $column => $value){

            if($value === null) {
                $properties .= $column . " IS NULL";
            } else if(is_int($value)) {
                $properties .= $column . " = ". $value;
            } else {
                $properties .= $column . " = '". $value . "'";
            }

            if($pointer < count($data)){
                $properties .= " AND ";
            };
            $pointer++;
        }

        return $properties;
    }

    private function compileInsertStatement($data)
    {
        $properties = "INSERT INTO {$this->table} (";

        $properties = $this->compileColumns($data, $properties);

        $properties .= "VALUES (";

        reset($data);

        $properties = $this->compileValues($data, $properties);

        return $properties;
    }

    private function compileColumns($data, $properties)
    {
        $pointer = 1;
        foreach(array_keys($data) as $column){

            $properties .= $column;

            if($pointer < count($data)){
                $properties .= ", ";
            } else {
                $properties .= ") ";
            };
            $pointer++;
        }

        return $properties;
    }

    private function compileValues($data, $properties)
    {
        $pointer = 1;
        foreach(array_keys($data) as $column){
            $properties .= ':'.$column;

            if($pointer < count($data)){
                $properties .= ", ";
            } else {
                $properties .= ")";
            };
            $pointer++;
        }

        return $properties;
    }

    private function setModelProperties($result)
    {
        if(count($result) === 1){
            foreach(array_shift($result) as $column => $value){
                $this->{$column} = $value;
            }
        }
    }

    public function isNotEmpty()
    {
        return count($this->content) > 0;
    }
}