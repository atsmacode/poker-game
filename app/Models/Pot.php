<?php

namespace App\Models;

class Pot extends Model
{

    use Collection;

    public $table = 'pots';
    public string $name;

    public function hand()
    {
        self::__construct($this->data);

        $this->getSelected($this->data);

        return Hand::find(['id' => $this->hand_id]);
    }

    public function table()
    {
        return $this->hand()->table();
    }

}