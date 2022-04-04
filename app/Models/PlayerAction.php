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

    public function tableSeat()
    {
        self::__construct($this->data);

        return TableSeat::find(['id' => $this->table_seat_id]);
    }

    public function action()
    {
        self::__construct($this->data);

        return Action::find(['id' => $this->action_id]);
    }

}