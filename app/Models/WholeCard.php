<?php

namespace App\Models;

class WholeCard extends Model
{

    use Collection;

    protected $table = 'whole_cards';

    public function card()
    {
        return Card::find(['id' => $this->card_id]);
    }

}