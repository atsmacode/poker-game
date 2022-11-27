<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Orm\Classes\Collection;
use Atsmacode\Orm\Classes\Model;
class PlayerActionLog extends Model
{
    use Collection;

    protected $table = 'player_action_logs';
    public $id;
}
