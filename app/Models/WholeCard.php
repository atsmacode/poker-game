<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\Framework\Collection\Collection;
use Atsmacode\Framework\Dbal\Model;
class WholeCard extends Model
{
    use Collection;

    protected  $table = 'whole_cards';
    public int $id;
    public int $card_id;
    public int $hand_id;
    public int $player_id;
}
