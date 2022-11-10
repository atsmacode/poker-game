<?php

namespace App\Helpers;

use App\Constants\Action as ConstantsAction;
use App\Models\Action;
use App\Models\Hand;
use App\Models\Player;
use App\Models\PlayerActionLog;
use App\Models\TableSeat;

class BetHelper
{
    public static function handle(Hand $hand, Player $player, $betAmount = null)
    {
        if($betAmount){
            $hand->pot()->update(['amount' => $hand->pot()->amount + $betAmount]);

            $stack = $player->stacks()->search('table_id', $hand->table()->id);

            $stack->update(['amount' => $stack->amount - $betAmount]);

            return $betAmount;
        }

        return null;
    }

    public static function postBlinds($hand, $smallBlind, $bigBlind)
    {
        PotHelper::initiatePot($hand);

        $smallBlind->update([
            'action_id'   => Action::find(['name' => 'Bet'])->id,
            'bet_amount'  => 25.0,
            'active'      => 1,
            'small_blind' => 1,
            'updated_at'  => date('Y-m-d H:i:s', strtotime('- 10 seconds'))
        ]);

        PlayerActionLog::create([
            'player_status_id' => $smallBlind->id,
            'bet_amount'       => 25.0,
            'small_blind'      => 1,
            'player_id'        => $smallBlind->player_id,
            'action_id'        => ConstantsAction::BET_ID,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $smallBlind->hand_street_id,
            'table_seat_id'    => $smallBlind->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time()),
        ]);

        TableSeat::find(['id' => $smallBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand, $smallBlind->player(), $smallBlind->bet_amount);

        $bigBlind->update([
            'action_id'  => Action::find(['name' => 'Bet'])->id,
            'bet_amount' => 50.0,
            'active'     => 1,
            'big_blind'  => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 5 seconds'))
        ]);

        PlayerActionLog::create([
            'player_status_id' => $bigBlind->id,
            'bet_amount'       => 50.0,
            'big_blind'        => 1,
            'player_id'        => $bigBlind->player_id,
            'action_id'        => ConstantsAction::BET_ID,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $bigBlind->hand_street_id,
            'table_seat_id'    => $bigBlind->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        TableSeat::find(['id' => $bigBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        BetHelper::handle($hand, $bigBlind->player(), $bigBlind->bet_amount);
    }
}
