<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class HandType extends Model
{
    use Collection;

    protected     $table = 'hand_types';
    public int    $id;
    public string $name;
    public int    $ranking;
}
