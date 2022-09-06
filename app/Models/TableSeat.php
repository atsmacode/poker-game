<?php

namespace App\Models;

class TableSeat extends Model
{

    use Collection;

    protected $table = 'table_seats';
    public string $name;
    public $player_id;

    public function player()
    {
        return Player::find(['id' => $this->player_id]);
    }
}