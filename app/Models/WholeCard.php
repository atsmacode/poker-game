<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class WholeCard extends Model
{
    use Collection;

    protected $table = 'whole_cards';
}
