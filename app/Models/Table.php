<?php

namespace App\Models;

class Table extends Model
{

    use Collection;

    protected $table = 'tables';
    public string $name;
    public $content;
    public $id;

    public function seats($stop = false)
    {
        self::__construct($this->data, $stop);

        return TableSeat::find(['table_id' => $this->id], $stop);
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