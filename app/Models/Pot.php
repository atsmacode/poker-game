<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Pot extends Model
{
    use Collection, CanBeModelled;

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