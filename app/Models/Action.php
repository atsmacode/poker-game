<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;

class Action extends PokerGameModel
{
    use Collection;

    public $table = 'actions';
    public string $name;
    public int $id;
}
