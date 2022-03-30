<?php

namespace App\Classes;

use PDO;
use PDOException;

class HandType
{

    use Connect;

    public string $name;
    public int $ranking;
    public array $content;

    public function __construct(string $name = null)
    {
        $this->setCredentials();
        $this->selectedName = $name;
        $this->select();
    }

    public function select()
    {
        if($this->selectedName){
            $this->getSelected();
        }
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = new self($value['name']);
        }
        return $this;
    }

    private function getSelected()
    {
        $rows = null;

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT * FROM hand_types
                    WHERE hand_types.name = '{$this->selectedName}'
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

        $this->name = $result['name'];
        $this->ranking = $result['ranking'];

        return $this;

    }

    public function all()
    {
        $rows = null;

        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT * FROM hand_types
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