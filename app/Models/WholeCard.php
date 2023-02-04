<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class WholeCard extends Model
{
    use Collection;

    protected string $table = 'whole_cards';
    private int      $card_id;
    private int      $hand_id;
    private int      $player_id;
}
