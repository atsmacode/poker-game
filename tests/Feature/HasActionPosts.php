<?php

namespace Tests\Feature;

use App\Constants\Action;
use App\Controllers\PlayerActionController;

trait HasActionPosts
{
    private function jsonResponse(): array
    {
        $response = (new PlayerActionController($this->actionHandler))->action();

        return json_decode($response, true)['body'];
    }

    private function setPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player1->id,
            'table_seat_id'  => $this->gameState->getSeats()[0]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[0]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerOneFoldsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player1->id,
            'table_seat_id'  => $this->gameState->getSeats()[0]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::FOLD_ID,
            'bet_amount'     => null,
            'active'         => 0,
            'stack'          => $this->gameState->getPlayers()[0]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerTwoCallsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player2->id,
            'table_seat_id'  => $this->gameState->getSeats()[1]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[1]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerTwoFoldsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player2->id,
            'table_seat_id'  => $this->gameState->getSeats()[1]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::FOLD_ID,
            'bet_amount'     => null,
            'active'         => 0,
            'stack'          => $this->gameState->getPlayers()[1]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerThreeChecksPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player3->id,
            'table_seat_id'  => $this->gameState->getSeats()[2]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::CHECK_ID,
            'bet_amount'     => null,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[2]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerThreeRaisesPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player3->id,
            'table_seat_id'  => $this->gameState->getSeats()[2]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::RAISE_ID,
            'bet_amount'     => 100,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[2]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourCallsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gameState->getSeats()[3]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::CALL_ID,
            'bet_amount'     => 50,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[3]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourFoldsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gameState->getSeats()[3]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::FOLD_ID,
            'bet_amount'     => null,
            'active'         => 0,
            'stack'          => $this->gameState->getPlayers()[3]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourRaisesPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gameState->getSeats()[3]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::RAISE_ID,
            'bet_amount'     => 100,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[3]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerFourChecksPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player4->id,
            'table_seat_id'  => $this->gameState->getSeats()[3]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::CHECK_ID,
            'bet_amount'     => null,
            'active'         => 1,
            'stack'          => $this->gameState->getPlayers()[3]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }

    private function setPlayerSixFoldsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $requestBody = [
            'deck'           => $this->gamePlay->dealer->getDeck(),
            'player_id'      => $this->player6->id,
            'table_seat_id'  => $this->gameState->getSeats()[5]['id'],
            'hand_street_id' => $this->gameState->updateHandStreets()->getHandStreets()->slice(0, 1)->id,
            'action_id'      => Action::FOLD_ID,
            'bet_amount'     => null,
            'active'         => 0,
            'stack'          => $this->gameState->getPlayers()[5]['stack']
        ];

        $_POST['body'] = serialize($requestBody);
    }
}