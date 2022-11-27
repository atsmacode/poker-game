<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;

class Action extends Model
{
    use Collection;

    public $table = 'actions';
    public string $name;
    public int $id;
}
