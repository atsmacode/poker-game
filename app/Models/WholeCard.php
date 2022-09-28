<?php

namespace App\Models;

class WholeCard extends Model
{
    use Collection;

    protected $table = 'whole_cards';

    public function card()
    {
        //var_dump($this->card_id);
        return Card::getById($this->card_id);
    }
}
