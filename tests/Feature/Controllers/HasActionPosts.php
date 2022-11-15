<?php

namespace Tests\Feature\Controllers;

use App\Constants\Action;

trait HasActionPosts
{
    private function setPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player1->id,
            'table_seat_id'  => $this->gamePlay->handTable->seats()->slice(0, 1)->id,
            'hand_street_id' => $this->gamePlay->hand->streets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50.0,
            'active'         => 1,
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourCallsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gamePlay->handTable->seats()->slice(3, 1)->id,
            'hand_street_id' => $this->gamePlay->hand->streets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50.0,
            'active'         => 1,
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourFoldsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gamePlay->handTable->seats()->slice(3, 1)->id,
            'hand_street_id' => $this->gamePlay->hand->streets()->slice(0, 1)->id,
            'action_id'      => Action::FOLD_ID,
            'bet_amount'     => null,
            'active'         => 0,
        ];

        $_POST['body'] = serialize($requestBody);
    }
}