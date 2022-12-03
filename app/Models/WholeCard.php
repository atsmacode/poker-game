<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class WholeCard extends PokerGameModel
{
    use Collection;

    protected $table = 'whole_cards';
}
