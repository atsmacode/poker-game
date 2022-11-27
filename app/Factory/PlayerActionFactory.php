<?php

namespace Atsmacode\PokerGame\Factory;

use Atsmacode\PokerGame\Models\PlayerAction;
use Atsmacode\PokerGame\Models\PlayerActionLog;

/**
 * A class to easily set player actions & associated logs in test suite.
 */
class PlayerActionFactory
{
    /**
     * @param int $playerActionId
     * @param int $handId
     * @param int $actionId
     * @param float|bool $betAmount
     * @param int $active
     */
    public static function create(
        $playerActionId,
        $handId,
        $actionId,
        $betAmount,
        $active
    ): PlayerAction {
        $playerAction = PlayerAction::find(['id' => $playerActionId]);

        $playerAction->update([
            'action_id'  => $actionId,
            'bet_amount' => $betAmount,
            'active'     => $active
        ]);

        PlayerActionLog::create([
            'player_status_id' => $playerAction->id,
            'bet_amount'       => $playerAction->bet_amount,
            'big_blind'        => $playerAction->big_blind,
            'small_blind'      => $playerAction->small_blind,
            'player_id'        => $playerAction->player_id,
            'action_id'        => $playerAction->action_id,
            'hand_id'          => $handId,
            'hand_street_id'   => $playerAction->hand_street_id,
            'table_seat_id'    => $playerAction->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        return $playerAction;
    }
}