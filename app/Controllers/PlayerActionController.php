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
        $hand         = Hand::latest();
        $playerAction = PlayerAction::find([
            'player_id'      =>  $_POST['player_id'],
            'table_seat_id'  =>  $_POST['table_seat_id'],
            'hand_street_id' => $_POST['hand_street_id']
        ]);

        $playerAction->update([
            'action_id'  => $_POST['action_id'],
            'bet_amount' => BetHelper::handle($hand, $_POST['player'], $_POST['bet_amount']),
            'active'     => $_POST['active'],
            'updated_at' => date('Y-m-d H:i:s', time())
        ]);

        $gamePlay = (new GamePlay($hand, $_POST['deck']))->play();

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
