<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class Pot extends Model
{
    use Collection;

    public $table = 'pots';
    public string $name;
    public $id;
}
