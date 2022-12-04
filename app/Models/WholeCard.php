<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class WholeCard extends Model
{
    use Collection, CanBeModelled;

    protected $table = 'whole_cards';
}
