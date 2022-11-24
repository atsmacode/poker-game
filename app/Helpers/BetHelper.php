<?php

namespace App\Helpers;

use App\Classes\GameState\GameState;
use App\Constants\Action as ConstantsAction;
use App\Models\Action;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\PlayerActionLog;
use App\Models\Stack;
use App\Models\TableSeat;

class BetHelper
{
    public static function handle(
        Hand $hand,
        int $stackAmount,
        int $playerId,
        int $tableId,
        int $betAmount = null
    ): ?int {
        if ($betAmount) {
            $stack  = $stackAmount - $betAmount;
            $pot    = $hand->pot();

            Stack::change($stack, $playerId, $tableId);
            $pot->update(['amount' => $pot->amount + $betAmount]);
        }
        
        return null;
    }

    public static function postBlinds(
        Hand $hand,
        PlayerAction $smallBlind,
        PlayerAction $bigBlind,
        GameState $gameState
    ): void {
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

        BetHelper::handle(
            $hand,
            $gameState->getStacks()[$smallBlind->player_id]->amount,
            $smallBlind->player_id,
            $hand->table_id,
            $smallBlind->bet_amount
        );

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

        BetHelper::handle(
            $hand,
            $gameState->getStacks()[$bigBlind->player_id]->amount,
            $bigBlind->player_id,
            $hand->table_id,
            $bigBlind->bet_amount
        );
    }
}
