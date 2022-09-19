<?php

namespace App\Controllers;

if (!isset($GLOBALS['dev'])) {
    require_once('../../vendor/autoload.php');
}

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

        /*
         * A hacky way to resolve updated_at not changing if the action_id i the same.
         * Issue happened when multiple rounds of re-raising takes place.
         * 
         * TDOD: don't think this is needed anymore now that I'm manually
         * doing a lot of SQL. Have created a setValue method if required.
         */
        //$playerAction->setValue('action_id', null);

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
