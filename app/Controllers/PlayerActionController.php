<?php

namespace App\Controllers;

use App\Classes\GamePlay;
use App\Helpers\BetHelper;
use App\Models\Hand;
use App\Models\PlayerAction;
use App\Models\PlayerActionLog;

class PlayerActionController
{
    public function action()
    {
        $requestBody = file_get_contents('php://input')
            ? json_decode(file_get_contents('php://input'), true)['body']
            : unserialize($_POST['body']);

        $hand         = Hand::latest();
        $playerAction = PlayerAction::find([
            'player_id'      =>  $requestBody['player_id'],
            'table_seat_id'  =>  $requestBody['table_seat_id'],
            'hand_street_id' => $requestBody['hand_street_id']
        ]);

        $playerAction->update([
            'action_id'  => $requestBody['action_id'],
            'bet_amount' => BetHelper::handle($hand, $playerAction->player(), $requestBody['bet_amount']),
            'active'     => $requestBody['active'],
            'updated_at' => date('Y-m-d H:i:s', time())
        ]);

        PlayerActionLog::create([
            'player_status_id' => $playerAction->id,
            'bet_amount'       => BetHelper::handle($hand, $playerAction->player(), $requestBody['bet_amount']),
            'big_blind'        => $playerAction->big_blind,
            'small_blind'      => $playerAction->small_blind,
            'player_id'        => $requestBody['player_id'],
            'action_id'        => $requestBody['action_id'],
            'hand_id'          => $hand->id,
            'hand_street_id'   => $requestBody['hand_street_id'],
            'table_seat_id'    => $requestBody['table_seat_id'],
            'created_at'       => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = (new GamePlay($hand, $requestBody['deck']))->play();

        if (!isset($GLOBALS['dev'])) {
            header("Content-Type: application/json");
            http_response_code(200);
        }

        $responseBody = [
            'deck'           => $gamePlay['deck'],
            'pot'            => $gamePlay['pot'],
            'communityCards' => $gamePlay['communityCards'],
            'players'        => $gamePlay['players'],
            'winner'         => $gamePlay['winner']
        ];

        if (isset($GLOBALS['dev'])) {
            return json_encode(['body' => $responseBody]);
        } else {
            echo json_encode(['body' => $responseBody]);
        }
    }
}

return (new PlayerActionController())->action();
