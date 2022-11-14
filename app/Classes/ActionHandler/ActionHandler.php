<?php declare(strict_types=1);

namespace App\Classes\ActionHandler;

use App\Classes\GameState\GameState;
use App\Helpers\BetHelper;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\PlayerActionLog;

class ActionHandler implements ActionHandlerInterface
{
    public GameState $gameState;

    public function __construct(GameState $gameState = null)
    {
        $this->gameState = $gameState;
    }

    public function handle(
        Hand $hand,
        int  $playerId,
        int  $tableSeatId,
        int  $handStreetId,
        int  $betAmount,
        int  $actionId,
        bool $active
    ): GameState {
        $playerAction = PlayerAction::find([
            'player_id'      =>  $playerId,
            'table_seat_id'  =>  $tableSeatId,
            'hand_street_id' => $handStreetId
        ]);

        $playerAction->update([
            'action_id'  => $actionId,
            'bet_amount' => BetHelper::handle($hand, $playerAction->player(), $betAmount),
            'active'     => $active,
            'updated_at' => date('Y-m-d H:i:s', time())
        ]);

        PlayerActionLog::create([
            'player_status_id' => $playerAction->id,
            'bet_amount'       => BetHelper::handle($hand, $playerAction->player(), $betAmount),
            'big_blind'        => $playerAction->big_blind,
            'small_blind'      => $playerAction->small_blind,
            'player_id'        => $playerId,
            'action_id'        => $actionId,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $handStreetId,
            'table_seat_id'    => $tableSeatId,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $this->gameState->setHand($hand);
        $this->gameState->setLatestAction($playerAction);

        return $this->gameState;
    }
}