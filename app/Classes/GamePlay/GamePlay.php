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
    public  $game;
    public  $dealer;
    private ?GameState $gameState;

    public function __construct($deck = null)
    {
        $this->game          = new PotLimitHoldEm();
        $this->dealer        = (new Dealer())->setDeck($deck);
        $this->start         = new Start($this->game, $this->dealer);
        $this->newStreet     = new NewStreet($this->game, $this->dealer);
        $this->showdown      = new Showdown($this->game, $this->dealer);
        $this->playerHandler = new PlayerHandler();
    }

    public function setGameState(GameState $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function response(HandStep $step = null, $currentDealer = null): array
    {
        $this->gameState = $step ? $step->handle($this->gameState, $currentDealer) : $this->gameState;

        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->gameState->getPot(),
            'communityCards' => $this->gameState->setCommunityCards()->getCommunityCards(),
            'players'        => $this->playerHandler->handle($this->gameState),
            'winner'         => $this->gameState->getWinner()
        ];
    }

    /** Specific start method to start new hand on page refresh in HandController */
    public function start(GameState $gameState = null, $currentDealer = null)
    {
        $this->gameState = $gameState;

        return $this->response($this->start, $currentDealer);
    }

    public function play(GameState $gameState, $currentDealer = null)
    {
        $this->gameState = $gameState;

        return $this->nextStep($currentDealer);
    }

    public function nextStep($currentDealer = null)
    {
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
        return count($this->gameState->getHandStreets()->content) === count($this->game->streets) &&
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
