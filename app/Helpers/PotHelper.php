<?php

namespace App\Helpers;

use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;

class PotHelper
{
    public static function initiatePot(Hand $hand)
    {
        Pot::create(['amount' => 0, 'hand_id' => $hand->id]);
    }

    public static function awardPot(Pot $pot, Player $player)
    {
        $stack = $player->stacks()->search('table_id', $pot->table()->id);

        $player->stacks()->search('table_id', $pot->table()->id)
            ->update(['amount' => $stack->amount + $pot->amount]);
    }
}
