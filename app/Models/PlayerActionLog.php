<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class PlayerActionLog extends Model
{
    use Collection;

    protected      $table = 'player_action_logs';
    public int     $id;
    public int     $player_action_id;
    public ?int    $bet_amount;
    public int     $big_blind;
    public int     $small_blind;
    public int     $player_id;
    public int     $action_id;
    public int     $hand_id;
    public int     $hand_street_id;
    public int     $table_seat_id;
    public string  $created_at;
}
