<?php

namespace App\Models;

class TableSeat extends Model
{

    protected $table = 'table_seats';
    public string $name;
    public $content;
    public $player_id;

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

    public function player($stop = false)
    {
        self::__construct($this->data, $stop);

        return new Player(['id' => $this->player_id], $stop);
    }

}