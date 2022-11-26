<?php

namespace App\Classes\GamePlay;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\PotLimitHoldEm;
use App\Classes\GameState\GameState;
use App\Classes\HandStep\HandStep;
use App\Classes\HandStep\NewStreet;
use App\Classes\HandStep\Showdown;
use App\Classes\HandStep\Start;
use App\Classes\PlayerHandler\PlayerHandler;
use App\Models\TableSeat;

/**
 * Responsible for deciding what happens next in a hand and 
 * providing the response to the front-end application.
 */
class GamePlay
{
    public function __construct(
        private GameState $gameState,
        private Start $start,
        private NewStreet $newStreet,
        private Showdown $showdown,
        private PlayerHandler $playerHandler,
        $deck = null
    ) {
        $this->gameState->setGame(new PotLimitHoldEm());
        $this->gameState->setGameDealer((new Dealer())->setDeck($deck));
    }

    public function setGameState(GameState $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function response(HandStep $step = null, $currentDealer = null): array
    {
        $this->gameState = $step ? $step->handle($this->gameState, $currentDealer) : $this->gameState;

        return [
            'deck'           => $this->gameState->getGameDealer()->getDeck(),
            'pot'            => $this->gameState->getPot(),
            'communityCards' => $this->gameState->setCommunityCards()->getCommunityCards(),
            'players'        => $this->playerHandler->handle($this->gameState),
            'winner'         => $this->gameState->getWinner()
        ];
    }

    /** Specific start method to start new hand on page refresh in HandController */
    public function start($currentDealer = null)
    {
        return $this->response($this->start, $currentDealer);
    }

    public function play(GameState $gameState, $currentDealer = null)
    {
        $this->gameState = $gameState;

        if ($this->theLastHandWasCompleted()) { return $this->response($this->start, $currentDealer); }

        $this->gameState->setPlayers();

        if ($this->theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()) {
            TableSeat::bigBlindWins($this->gameState->handId());

            return $this->response($this->showdown);
        }

        if ($this->readyForShowdown() || $this->onePlayerRemainsThatCanContinue()) { return $this->response($this->showdown); }

        if ($this->allActivePlayersCanContinue()) { return $this->response($this->newStreet); }

        return $this->response();
    }

    protected function readyForShowdown()
    {
        return count($this->gameState->getHandStreets()->content) === count($this->gameState->getGame()->streets) &&
            count($this->gameState->getActivePlayers()) === count($this->gameState->getContinuingPlayers());
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return count($this->gameState->getActivePlayers()) === count($this->gameState->getContinuingPlayers())
            && count($this->gameState->getContinuingPlayers()) === 1;
    }

    protected function allActivePlayersCanContinue()
    {
        return count($this->gameState->getActivePlayers()) === count($this->gameState->getContinuingPlayers());
    }

    protected function theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()
    {
        $activePlayers = array_values(array_filter($this->gameState->getPlayers(), function($player){
            return 1 === $player['active'];
        }));

        return 1 === count($activePlayers) && 1 === $activePlayers[0]['big_blind'];
    }

    protected function theLastHandWasCompleted()
    {
        return $this->gameState->getHand()->completed_on;
    }
}
