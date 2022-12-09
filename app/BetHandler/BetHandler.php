<?php

namespace Atsmacode\PokerGame\BetHandler;

use Atsmacode\Framework\Database\Database;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\PlayerAction;
use Atsmacode\PokerGame\Models\PlayerActionLog;
use Atsmacode\PokerGame\Models\Stack;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\PotHandler\PotHandler;

class BetHandler extends Database
{
    public function __construct(
        private PotHandler      $potHandler,
        private PlayerActionLog $playerActionLogModel,
        private Stack           $stackModel,
        private TableSeat       $tableSeatModel
    ) {}

    /** @todo Don't need the entire hand model, can pass ID */
    public function handle(
        Hand $hand,
        int $stackAmount,
        int $playerId,
        int $tableId,
        int $betAmount = null
    ): ?int {
        if ($betAmount) {
            $stack  = $stackAmount - $betAmount;

            $this->stackModel->change($stack, $playerId, $tableId);
            $this->potHandler->updatePot($betAmount, $hand->id);
        }
        
        return null;
    }

    public function postBlinds(
        Hand $hand,
        PlayerAction $smallBlind,
        PlayerAction $bigBlind,
        GameState $gameState
    ): void {
        $this->potHandler->initiatePot($hand);

        $smallBlind->update([
            'action_id'   => Action::BET_ID,
            'bet_amount'  => 25.0,
            'active'      => 1,
            'small_blind' => 1,
            'updated_at'  => date('Y-m-d H:i:s', strtotime('- 10 seconds'))
        ]);

        //var_dump($smallBlind);

        $this->playerActionLogModel->create([
            'player_status_id' => $smallBlind->id,
            'bet_amount'       => 25.0,
            'small_blind'      => 1,
            'player_id'        => $smallBlind->player_id,
            'action_id'        => Action::BET_ID,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $smallBlind->hand_street_id,
            'table_seat_id'    => $smallBlind->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time()),
        ]);

        $this->tableSeatModel->find(['id' => $smallBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        $this->handle(
            $hand,
            $gameState->getStacks()[$smallBlind->player_id]->amount,
            $smallBlind->player_id,
            $hand->table_id,
            $smallBlind->bet_amount
        );

        $bigBlind->update([
            'action_id'  => Action::BET_ID,
            'bet_amount' => 50.0,
            'active'     => 1,
            'big_blind'  => 1,
            'updated_at' => date('Y-m-d H:i:s', strtotime('- 5 seconds'))
        ]);

        //var_dump($bigBlind);

        $this->playerActionLogModel->create([
            'player_status_id' => $bigBlind->id,
            'bet_amount'       => 50.0,
            'big_blind'        => 1,
            'player_id'        => $bigBlind->player_id,
            'action_id'        => Action::BET_ID,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $bigBlind->hand_street_id,
            'table_seat_id'    => $bigBlind->table_seat_id,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $this->tableSeatModel->find(['id' => $bigBlind->table_seat_id])
            ->update([
                'can_continue' => 0
            ]);

        $this->handle(
            $hand,
            $gameState->getStacks()[$bigBlind->player_id]->amount,
            $bigBlind->player_id,
            $hand->table_id,
            $bigBlind->bet_amount
        );
    }
}
