<?php

namespace App\Models;

class HandStreetCard extends Model
{

    use Collection;

    protected $table = 'hand_street_cards';

    public function card()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return Card::find(['id' => $this->card_id]);
    }

}