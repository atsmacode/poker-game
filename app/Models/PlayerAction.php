<?php

namespace App\Models;

use PDO;
use PDOException;

class PlayerAction extends Model
{

    use Collection;

    protected $table = 'player_actions';
    public $id;

    public function player()
    {
        return Player::find(['id' => $this->player_id]);
    }

    public function tableSeat()
    {
        return TableSeat::find(['id' => $this->table_seat_id]);
    }

    public function action()
    {
        return Action::find(['id' => $this->action_id]);
    }
}
