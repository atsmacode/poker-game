<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class HandType extends Model
{
    use Collection;

    protected $table = 'hand_types';
    public string $name;
    public $ranking;
}