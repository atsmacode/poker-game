<?php

namespace App\Models;

class Table extends Model
{

    protected $table = 'tables';
    public string $name;
    public $content;
    public $id;

    public function __construct(array $data = null,$stop = false)
    {
        $this->data = $data;
        $this->stop = $stop;
        $this->initiate();
    }

    public function initiate()
    {
        $this->findOrCreate($this->data);
    }

    public function collect()
    {
        foreach($this->content as $key => $value){
            $this->content[$key] = is_a($value, self::class) ? $value : new self($value);
        }
        return $this;
    }

    public function seats($stop = false)
    {
        self::__construct($this->data, $stop);

        return new TableSeat(['table_id' => $this->id], $stop);
    }

    public function players()
    {
        $players = new Player([], true);

        foreach($this->seats(true)->collect()->content as $seat){
            $players->content[] = $seat->player(true);
        }

        return $players;
    }

}