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

            $hand->pot->increment('amount', $betAmount);
            $player->fresh()->stacks
                ->where('table_id', $hand->handTable->id)
                ->first()
                ->decrement('amount', $betAmount);

            return $betAmount;

        }

        return null;
    }

    public static function postBlinds($hand, $smallBlind, $bigBlind)
    {

        PotHelper::initiatePot($hand);

        /*
         * Using ->save rather than ->update so the updated_at
         * value can be checked against and set the action_on
         * player correctly.
         */
        $smallBlind->action_id = Action::where('name', 'Bet')->first()->id; // Bet
        $smallBlind->bet_amount = 25.0;
        $smallBlind->active = 1;
        $smallBlind->small_blind = 1;
        $smallBlind->updated_at = date('Y-m-d H:i:s', strtotime('- 10 seconds'));
        $smallBlind->save();

        TableSeat::where('id', $smallBlind->table_seat_id)
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand->fresh(), $smallBlind->player->fresh(), $smallBlind->bet_amount);

        $bigBlind->action_id = Action::where('name', 'Bet')->first()->id; // Bet
        $bigBlind->bet_amount = 50.0;
        $bigBlind->active = 1;
        $bigBlind->big_blind = 1;
        $bigBlind->updated_at = date('Y-m-d H:i:s', strtotime('- 5 seconds'));
        $bigBlind->save();

        TableSeat::where('id', $bigBlind->table_seat_id)
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand->fresh(), $bigBlind->player->fresh(), $bigBlind->bet_amount);

    }
}
