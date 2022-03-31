<?php

namespace App\Models;

class HandType extends Model
{

    protected $table = 'hand_types';
    public string $name;
    public $ranking;
    public $content;

    public function __construct(string $selected = null)
    {
        $this->selected = $selected;
        $this->initiate();
    }

    public function initiate()
    {

        $this->findOrCreate('name');

        if($this->content){
            $this->ranking = $this->content['ranking'];
            $this->name = $this->content['name'];
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