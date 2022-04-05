<?php

namespace App\Models;

class Player extends Model
{

    use Collection;

    protected $table = 'players';
    public $id;

    public function wholeCards()
    {
        return WholeCard::find(['player_id' => $this->id]);
    }

    public function actions()
    {
        return PlayerAction::find(['player_id' => $this->id]);
    }

    public function stacks()
    {
        return Stack::find(['player_id' => $this->id]);
    }

}