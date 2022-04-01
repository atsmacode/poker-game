<?php

namespace App\Models;

class Player extends Model
{

    protected $table = 'players';
    public $content;
    public $id;

    public function __construct(array $data = null, $stop = false)
    {
        $this->data = $data;
        $this->stop = $stop;
        $this->initiate();
    }

    public function initiate()
    {
        $this->findOrCreate($this->data, $this->stop);
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = is_a($value, self::class) ? $value : new self($value);
        }
        return $this;
    }

    public function wholeCards($stop = false)
    {
        self::__construct($this->data, $stop);

        return new WholeCard(['player_id' => $this->id], $stop);
    }

}