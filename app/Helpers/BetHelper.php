<?php

namespace App\Helpers;

use App\Models\Action;
use App\Models\Hand;
use App\Models\Player;
use App\Models\TableSeat;

class BetHelper
{
    public static function handle(Hand $hand, Player $player, $betAmount = null)
    {
        if($betAmount){

            $hand->pot()->update(['amount' => $betAmount]);

            $player->stacks()->search('table_id', $hand->table()->id)
                ->update(['amount' => $betAmount]);

            return $betAmount;

        }

        return null;
    }

    public static function postBlinds($hand, $smallBlind, $bigBlind)
    {

        PotHelper::initiatePot($hand);

        $smallBlind->update([
            'action_id' => Action::find(['name' => 'Bet'])->id,
            'bet_amount' => 25.0,
            'active' => 1,
            'small_blind' => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 10 seconds'))
        ]);

        TableSeat::find(['id' => $smallBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand, $smallBlind->player(), $smallBlind->bet_amount);

        $bigBlind->update([
            'action_id' => Action::find(['name' => 'Bet'])->id,
            'bet_amount' => 50.0,
            'active' => 1,
            'big_blind' => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 5 seconds'))
        ]);

        TableSeat::find(['id' => $bigBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand, $bigBlind->player(), $bigBlind->bet_amount);

    }
}
