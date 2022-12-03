<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class HandType extends PokerGameModel
{
    use Collection;

    protected $table = 'hand_types';
    public string $name;
    public $ranking;
}