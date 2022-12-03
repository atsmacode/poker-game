<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class Pot extends PokerGameModel
{
    use Collection;

    public $table = 'pots';
    public string $name;
    public $id;

    public function hand()
    {
        return Hand::find(['id' => $this->hand_id]);
    }

    public function table()
    {
        return $this->hand()->table();
    }
}