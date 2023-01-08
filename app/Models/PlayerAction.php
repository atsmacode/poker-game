<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class PlayerAction extends Model
{
    use Collection;

    protected      $table = 'player_actions';
    public int     $id;
    public ?int    $bet_amount;
    public int     $active;
    public int     $big_blind;
    public int     $small_blind;
    public int     $player_id;
    public ?int    $action_id;
    public int     $hand_id;
    public int     $hand_street_id;
    public int     $table_seat_id;
    public ?string $updated_at;
}
