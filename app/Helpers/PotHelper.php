<?php

namespace Atsmacode\PokerGame\Helpers;

use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Pot;
use Atsmacode\PokerGame\Models\Stack;

class PotHelper
{
    public static function initiatePot(Hand $hand): void
    {
        Pot::create(['amount' => 0, 'hand_id' => $hand->id]);
    }

    public static function awardPot(int $stackAmount, int $potAmount, int $playerId, int $tableId): void
    {
        $amount = $stackAmount + $potAmount;

        Stack::change($amount, $playerId, $tableId);
    }
}
