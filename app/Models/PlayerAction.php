<?php

namespace App\Models;

class PlayerAction extends Model
{

    use Collection;

    protected $table = 'player_actions';
    public $id;

    public function player()
    {
        self::__construct($this->data);

        return Player::find(['id' => $this->player_id]);
    }

}