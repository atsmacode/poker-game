<?php

namespace App\Models;

class Player extends Model
{

    protected $table = 'players';
    public $content;
    public $id;

    public function __construct(array $data = null)
    {
        $this->data = $data;
        $this->initiate();
    }

    public function initiate()
    {
        $this->findOrCreate($this->data);
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = new self($value);
        }
        return $this;
    }

    public function wholeCards($stop = false)
    {
        return (new WholeCard(['player_id' => $this->id], $stop));
    }

}