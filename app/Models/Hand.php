<?php

namespace App\Models;

class Hand extends Model
{

    use Collection;

    protected $table = 'hands';
    public $id;

    public function streets()
    {
        return HandStreet::find(['hand_id' => $this->id]);
    }

    public function table()
    {
        return Table::find(['id' => $this->table_id]);
    }

    public function actions()
    {
        return PlayerAction::find(['hand_id' => $this->id]);
    }

    public function pot()
    {
        return Pot::find(['hand_id' => $this->id]);
    }

}