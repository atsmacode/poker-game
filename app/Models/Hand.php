<?php

namespace App\Models;

class Hand extends Model
{

    use Collection;

    protected $table = 'hands';
    public $id;

    public function streets()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return HandStreet::find(['hand_id' => $this->id]);
    }

    public function table()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return Table::find(['id' => $this->table_id]);
    }

    public function actions()
    {
        self::__construct($this->data);

        return PlayerAction::find(['hand_id' => $this->id]);
    }

    public function pot()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return Pot::find(['hand_id' => $this->id]);
    }

}