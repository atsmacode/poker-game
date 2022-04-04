<?php

namespace App\Helpers;

use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;

class PotHelper
{
    public static function initiatePot(Hand $hand)
    {
        $hand->pot()->create(['amount' => 0]);
    }

    public static function awardPot(Pot $pot, Player $player)
    {
        $player->stacks
            ->where('table_id', $pot->hand->handTable->id)
            ->first()
            ->increment('amount', $pot->amount);
    }
}
