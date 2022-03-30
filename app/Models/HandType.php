<?php

namespace App\Models;

use App\Classes\CustomPDO;
use App\Traits\Connect;
use PDO;
use PDOException;

class HandType extends Model
{

    use Connect;

    public $table = 'hand_types';
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
            $this->getSelected('name', $this->selectedName);
            $this->ranking = $this->content['ranking'];
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