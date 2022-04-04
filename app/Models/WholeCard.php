<?php

namespace App\Models;

class WholeCard extends Model
{

    use Collection;

    protected $table = 'whole_cards';

    public function card()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return Card::find(['id' => $this->card_id]);
    }

}