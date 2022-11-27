<?php

namespace Atsmacode\PokerGame\Models;

class Street extends Model
{
    use Collection;

    public $table = 'streets';
    public string $name;
    public $id;
}