<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\Classes\Database;
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
            error_log(__METHOD__ . $e->getMessage());
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
            error_log(__METHOD__ . ': ' . $e->getMessage());
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

            $stmt->execute();
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
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
                $stmt->bindParam(':'.$column, $value);
            }

            $stmt->execute();
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
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
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }

        $result = $rows;
        $this->content = $result;

        return $this;
    }

    /**
     * Created this to help with setting NULL values if
     * required. The condition if($value !== null){ causes
     * problem from time to time in the other methods.
     * 
     * TODO ^
     *
     * @param string $column
     * @param string $value
     * @return self
     */
    public function setValue($column, $value)
    {
        $query = sprintf("
            UPDATE
                {$this->table}
            SET
                {$column} = :value
            WHERE
                {$this->table}.id = :id
            LIMIT
                1
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':value', $value);
            $stmt->execute();

            return $this;
        } catch(PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
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
    
        $properties .= " WHERE id = {$this->id}";

        return $properties;
    }

    private function compileUpdateBatchStatement($data, $where = null)
    {
        $properties = "UPDATE {$this->table} SET ";

        $pointer = 1;
        foreach($data as $column => $value){
            $properties .= $column . " = :". $column;

            if($pointer < count($data)){
                $properties .= ", ";
            };
            $pointer++;
        };

        if ($where) {
            $properties .= " WHERE {$where}";
        }

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

    protected function setModelProperties($result)
    {
        if(count($result) === 1){
            foreach (array_shift($result) as $column => $value) {
                $this->{$column} = $value;
            }
        }
    }

    public function isNotEmpty()
    {
        return count($this->content) > 0;
    }
}
