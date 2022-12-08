<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class HandType extends Model
{
    use Collection;

    protected $table = 'hand_types';
    public string $name;
    public $ranking;
}
