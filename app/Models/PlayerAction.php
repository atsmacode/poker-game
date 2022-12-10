<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class PlayerAction extends Model
{
    use Collection;

    protected $table = 'player_actions';
    public $id;
}
