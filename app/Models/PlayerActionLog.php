<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class PlayerActionLog extends Model
{
    use Collection, CanBeModelled;

    protected $table = 'player_action_logs';
    public $id;
}
