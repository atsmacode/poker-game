<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\PokerGame\Models\PokerGameModel;
class PlayerActionLog extends PokerGameModel
{
    use Collection;

    protected $table = 'player_action_logs';
    public $id;
}
