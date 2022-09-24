<?php

namespace App\Controllers;

use App\Classes\GamePlay;
use App\Helpers\BetHelper;
use App\Models\Hand;
use App\Models\PlayerAction;

class PlayerActionController
{
    public function action()
    {
        $requestBody = file_get_contents('php://input')
            ? (array) json_decode(file_get_contents('php://input'))
            : unserialize(json_decode($_POST['body'], true));

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

        $gamePlay = (new GamePlay($hand, $requestBody['deck']))->play();

        if (!isset($GLOBALS['dev'])) {
            header("Content-Type: application/json");
            http_response_code(200);
        }

        return json_encode([
            'deck'           => $gamePlay['deck'],
            'pot'            => $gamePlay['pot'],
            'communityCards' => $gamePlay['communityCards'],
            'players'        => $gamePlay['players'],
            'winner'         => $gamePlay['winner']
        ]);
    }
}

return (new PlayerActionController())->action();
