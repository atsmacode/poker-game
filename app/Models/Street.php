<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class Street extends PokerGameModel
{
    use Collection;

    public $table = 'streets';
    public string $name;
    public $id;
}