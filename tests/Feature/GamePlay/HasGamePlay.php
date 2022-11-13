<?php

namespace Tests\Feature\GamePlay;

use App\Constants\Action;
use App\Factory\PlayerActionFactory;
use App\Models\TableSeat;

trait HasGamePlay
{
    private function givenPlayerOneRaisesBigBlind()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::RAISE_ID,
            betAmount:      100.0,
            active:         1,
        );
    }

    private function givenPlayerOneFolds()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );
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
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(0, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );
    }

    private function givenPlayerTwoCalls()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(1, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );
    }

    private function givenPlayerTwoFolds()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(1, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );
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
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );
    }

    private function givenPlayerThreeCallsSmallBlind()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      25.00,
            active:         1,
        );
    }

    private function givenPlayerThreeRaises()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(2, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::RAISE_ID,
            betAmount:      100.00,
            active:         1,
        );
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
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::FOLD_ID,
            betAmount:      null,
            active:         0,
        );
    }

    private function givenPlayerFourCalls()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CALL_ID,
            betAmount:      50.00,
            active:         1,
        );
    }

    private function givenPlayerFourChecks()
    {
        PlayerActionFactory::create(
            playerActionId: $this->gamePlay->hand->actions()->slice(3, 1)->id,
            handId:         $this->gamePlay->handId,
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );
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

    protected function executeActionsToContinue()
    {
        $this->givenPlayerOneCalls();
        $this->givenPlayerOneCanContinue();

        $this->givenPlayerTwoFolds();
        $this->givenPlayerTwoCanNotContinue();

        $this->givenPlayerThreeChecks();
        $this->givenPlayerThreeCanContinue();
    }
}