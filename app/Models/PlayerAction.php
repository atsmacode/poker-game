<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class PlayerAction extends PokerGameModel
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
