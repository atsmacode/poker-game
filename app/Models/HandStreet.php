<?php

namespace App\Models;

class HandStreet extends Model
{

    use Collection;

    protected $table = 'hand_streets';
    public $id;

    public function cards()
    {
        self::__construct($this->data);

        return HandStreetCard::find(['hand_street_id' => $this->id]);
    }

}