<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class Pot extends Model
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