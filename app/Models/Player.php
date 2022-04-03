<?php

namespace App\Models;

class Player extends Model
{

    use Collection;

    protected $table = 'players';
    public $id;

    public function wholeCards($stop = false)
    {
        self::__construct($this->data, $stop);

        return WholeCard::find(['player_id' => $this->id], $stop);
    }

    public function actions($stop = false)
    {
        self::__construct($this->data, $stop);

        return PlayerAction::find(['player_id' => $this->id], $stop);
    }

    public function stacks($stop = false)
    {
        self::__construct($this->data, $stop);

        return Stack::find(['player_id' => $this->id], $stop);
    }

}