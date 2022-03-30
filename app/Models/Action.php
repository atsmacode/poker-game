<?php

namespace App\Models;

use App\Classes\CustomPDO;
use App\Traits\Connect;
use PDO;
use PDOException;

class Action extends Model
{

    use Connect;

    public $table = 'actions';
    public string $name;

    public function __construct(string $name = null)
    {
        $this->setCredentials();
        $this->selectedName = $name;
        $this->select();
    }

    public function select()
    {
        if($this->selectedName){
            $this->getSelected('name', $this->selectedName);
        }
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = new self($value['name']);
        }
        return $this;
    }

}