<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;

class Action extends Model
{
    use Collection;

    protected string $table = 'actions';
    public int       $id;
    public string    $name;
}
