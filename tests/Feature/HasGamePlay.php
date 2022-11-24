<?php

namespace Tests\Feature;

use App\Constants\Action;
use App\Factory\PlayerActionFactory;
use App\Models\TableSeat;
use App\Models\WholeCard;

trait HasGamePlay
{
    private function givenPlayerOneRaisesBigBlind()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[0]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::RAISE_ID,
            betAmount:      100,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerOneFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[0]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerOneCanNotContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[0]['id']])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerOneCanContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[0]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerOneCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[0]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      50,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[1]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      50,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[1]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoCanNotContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[1]['id']])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerTwoCanContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[1]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerThreeChecks()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[2]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeCallsSmallBlind()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[2]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      25,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeRaises()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[2]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::RAISE_ID,
            betAmount:      100,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeCanContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[2]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerFourFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[3]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[3]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourChecks()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[3]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourRaises()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[3]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::RAISE_ID,
            betAmount:      100,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourCanContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[3]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerFourCanNotContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[3]['id']])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerFiveCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gameState->getPlayers()[4]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      50,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFiveCanContinue()
    {
        TableSeat::find(['id' => $this->gameState->getSeats()[4]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    protected function setWholeCards($wholeCards)
    {
        foreach($wholeCards as $card){
            WholeCard::create([
                'player_id' => $card['player']->id,
                'card_id'   => $card['card_id'],
                'hand_id'   => $this->gameState->handId()
            ]);
        }
    }
}