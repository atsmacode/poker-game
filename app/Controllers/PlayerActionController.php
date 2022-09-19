<?php

namespace App\Controllers;

if (!isset($GLOBALS['dev'])) {
    require_once('../../vendor/autoload.php');
}

use App\Classes\GamePlay;
use App\Models\Hand;

class PlayerActionController
{
    public function action()
    {
        $hand     = Hand::latest();
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
