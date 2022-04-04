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

}