<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class Street extends Model
{
    use Collection;

    public $table = 'streets';
    public string $name;
    public $id;
}