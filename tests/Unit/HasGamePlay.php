<?php

namespace Atsmacode\PokerGame\Tests\Unit;

use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\PokerGame\Game\PotLimitHoldEm;
use Atsmacode\PokerGame\GamePlay\GamePlay;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;
use Atsmacode\PokerGame\Factory\PlayerActionFactory;
use Atsmacode\PokerGame\Models\HandStreet;

trait HasGamePlay
{
    private function createPlayer(int $player)
    {
        $playerModel = $this->container->build(Player::class);

        return $playerModel->create([
            'name'  => 'Player ' . $player,
            'email' => sprintf('player%d@rrh.com', $player)
        ]);
    }

    private function setGamePlay()
    {
        $this->gameState = $this->container->build(GameState::class, [
            'hand' => $this->hand,
        ]);
        
        $this->gamePlay = $this->container->build(GamePlay::class, [
            'game'      => $this->container->get(PotLimitHoldEm::class),
            'gameState' => $this->gameState,
        ]);
    }

    private function isThreeHanded()
    {
        $this->tableModel          = $this->container->build(Table::class);
        $this->handModel           = $this->container->build(Hand::class);
        $this->playerModel         = $this->container->build(Player::class);
        $this->tableSeatModel      = $this->container->build(TableSeat::class);
        $this->handStreetModel     = $this->container->build(HandStreet::class);
        $this->playerActionFactory = $this->container->build(PlayerActionFactory::class);

        $this->table = $this->tableModel->create(['name' => 'Test Table', 'seats' => 3]);
        $this->hand  = $this->handModel->create(['table_id' => $this->table->id]);

        $this->player1 = $this->createPlayer(1);
        $this->player2 = $this->createPlayer(2);
        $this->player3 = $this->createPlayer(3);

        $this->tableSeatOne = $this->tableSeatModel->create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player1->id
        ]);

        $this->tableSeatTwo = $this->tableSeatModel->create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player2->id
        ]);

        $this->tableSeatThree =$this->tableSeatModel->create([
            'table_id'  => $this->table->id,
            'player_id' => $this->player3->id
        ]);

        $this->setGamePlay();
    }

    private function givenPlayerOneCanContinue()
    {
        $this->tableSeatModel->find(['id' => $this->gameState->getSeats()[0]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }

    private function givenPlayerOneCalls()
    {
        $playerAction = $this->playerActionFactory->create(
            playerActionId: $this->gameState->getPlayers()[0]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CALL_ID,
            betAmount:      50,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerTwoFolds()
    {
        $playerAction = $this->playerActionFactory->create(
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
        $this->tableSeatModel->find(['id' => $this->gameState->getSeats()[1]['id']])
            ->update([
                'can_continue' => 0
            ]);
    }

    private function givenPlayerThreeChecks()
    {
        $playerAction = $this->playerActionFactory->create(
            playerActionId: $this->gameState->getPlayers()[2]['player_action_id'],
            handId:         $this->gameState->handId(),
            actionId:       Action::CHECK_ID,
            betAmount:      null,
            active:         1,
        );

        $this->gameState->setLatestAction($playerAction);
    }

    private function givenPlayerThreeCanContinue()
    {
        $this->tableSeatModel->find(['id' => $this->gameState->getSeats()[2]['id']])
            ->update([
                'can_continue' => 1
            ]);
    }
}