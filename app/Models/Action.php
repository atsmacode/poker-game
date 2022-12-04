<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Action extends Model
{
    use Collection, CanBeModelled;

    public $table = 'actions';
    public string $name;
    public int $id;
}
