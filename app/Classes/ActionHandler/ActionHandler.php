<?php declare(strict_types=1);

namespace App\Classes\ActionHandler;

use App\Classes\GameState\GameState;
use App\Constants\Action;
use App\Helpers\BetHelper;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\PlayerActionLog;
use App\Models\TableSeat;

class ActionHandler implements ActionHandlerInterface
{
    public GameState $gameState;

    public function __construct(GameState $gameState = null)
    {
        $this->gameState = $gameState;
    }

    /**
     * @param $int|null $betAmount
     */
    public function handle(
        Hand $hand,
        int  $playerId,
        int  $tableSeatId,
        int  $handStreetId,
             $betAmount,
        int  $actionId,
        int  $active,
        int  $stack
    ): GameState {
        $playerAction = PlayerAction::find([
            'player_id'      =>  $playerId,
            'table_seat_id'  =>  $tableSeatId,
            'hand_street_id' =>  $handStreetId
        ]);

        BetHelper::handle($hand, $stack, $playerId, $hand->table_id, $betAmount);

        $playerAction->update([
            'action_id'  => $actionId,
            'bet_amount' => $betAmount,
            'active'     => $active,
            'updated_at' => date('Y-m-d H:i:s', time())
        ]);

        PlayerActionLog::create([
            'player_status_id' => $playerAction->id,
            'bet_amount'       => $betAmount,
            'big_blind'        => $playerAction->big_blind,
            'small_blind'      => $playerAction->small_blind,
            'player_id'        => $playerId,
            'action_id'        => $actionId,
            'hand_id'          => $hand->id,
            'hand_street_id'   => $handStreetId,
            'table_seat_id'    => $tableSeatId,
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $this->gameState->initiate($hand);
        $this->gameState->setLatestAction($playerAction);

        $this->updateSeatStatusOfLatestAction();
        $this->updateAllOtherSeatsBasedOnLatestAction();

        return $this->gameState;
    }

    private function updateSeatStatusOfLatestAction()
    {
        switch($this->gameState->getLatestAction()->action_id){
            case Action::CHECK['id']:
            case Action::CALL['id']:
            case Action::BET['id']:
            case Action::RAISE['id']:
                $canContinue = 1;
                break;
            default:
                $canContinue = 0;
                break;
        }

        TableSeat::find(['id' => $this->gameState->getLatestAction()->table_seat_id])
            ->update([
                'can_continue' => $canContinue
            ]);
    }

    private function updateAllOtherSeatsBasedOnLatestAction()
    {
        switch($this->gameState->getLatestAction()->action_id){
            case Action::BET['id']:
            case Action::RAISE['id']:
                $canContinue = 0;
                break;
            default:
                break;
        }

        if(isset($canContinue)){
            $tableSeats = TableSeat::find(['table_id' => $this->gameState->getHand()->table()->id]);
            $tableSeats->updateBatch([
                'can_continue' => $canContinue
            ], 'id != ' . $this->gameState->getLatestAction()->table_seat_id);
        }
    }
}