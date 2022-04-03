<?php

namespace App\Models;

class Player extends Model
{

    use Collection;

    protected $table = 'players';
    public $id;

    public function wholeCards()
    {
        self::__construct($this->data);

        return WholeCard::find(['player_id' => $this->id]);
    }

    public function actions()
    {
        self::__construct($this->data);

        return PlayerAction::find(['player_id' => $this->id]);
    }

    public function stacks()
    {
        self::__construct($this->data);

        return Stack::find(['player_id' => $this->id]);
    }

}