<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class PlayerActionLog extends Model
{
    use Collection;

    protected $table = 'player_action_logs';
    public $id;
}
