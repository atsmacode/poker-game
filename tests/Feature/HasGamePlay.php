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
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::RAISE_ID,
            betAmount:      100.0,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerOneFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerOneCanNotContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerOneCanContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(0, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerOneCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(1, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(1, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoCanNotContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerTwoCanContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(1, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerThreeChecks()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeCallsSmallBlind()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      25.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeRaises()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::RAISE_ID,
            betAmount:      100.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeCanContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(2, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerFourFolds()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourChecks()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourRaises()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::RAISE_ID,
            betAmount:      100.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFourCanContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerFourCanNotContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(3, 1)->id])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerFiveCalls()
    {
        $playerAction = PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(4, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerFiveCanContinue()
    {
        TableSeat::find(['id' => $this->gamePlay->handTable->seats()->slice(4, 1)->id])
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
                'hand_id'   => $this->gamePlay->hand->id
            ]);
        }
    }
}