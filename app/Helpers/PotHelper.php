<?php

namespace App\Helpers;

use App\Models\Hand;
use App\Models\Player;
use App\Models\Pot;

class PotHelper
{
    public static function initiatePot(Hand $hand)
    {
        $hand->pot()::create(['amount' => 0, 'hand_id' => $hand->id]);
    }

    public static function awardPot(Pot $pot, Player $player)
    {
        $player->stacks()->search(['table_id' => $pot->hand()->table()])
            ->update(['amount' => $pot->amount]); // To be changed to increment
    }
}
