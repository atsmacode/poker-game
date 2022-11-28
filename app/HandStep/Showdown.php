<?php

namespace Atsmacode\PokerGame\HandStep;

use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Showdown\Showdown as TheShowdown;
use Atsmacode\PokerGame\Helpers\PotHelper;
use Atsmacode\PokerGame\Models\TableSeat;

/**
 * Responsible for the actions required if the hand has reached a showdown.
 */
class Showdown extends HandStep
{
    public function handle(GameState $gameState, TableSeat $currentDealer = null): GameState
    {
        $this->gameState = $gameState;
        
        $this->gameState->setPlayers();

        $winner = (new TheShowdown($this->gameState))->compileHands()->decideWinner();

        $this->gameState->setWinner($winner);

        PotHelper::awardPot(
            $winner['player']['stack'],
            $this->gameState->getPot(),
            $winner['player']['player_id'],
            $winner['player']['table_id']
        );

        $this->gameState->getHand()->complete();

        return $this->gameState;
    }
}