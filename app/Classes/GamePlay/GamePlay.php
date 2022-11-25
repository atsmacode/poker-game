<?php

namespace App\Classes\GamePlay;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\PotLimitHoldEm;
use App\Classes\GameState\GameState;
use App\Classes\HandStep\InProgress;
use App\Classes\HandStep\Showdown;
use App\Classes\HandStep\Start;
use App\Classes\PlayerHandler\PlayerHandler;
use App\Models\TableSeat;

/**
 * Responsible for deciding what happens next in a hand.
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
        $this->inProgress    = new InProgress($this->game, $this->dealer);
        $this->showdown      = new Showdown($this->game, $this->dealer);
        $this->playerHandler = new PlayerHandler();
    }

    public function setGameState(GameState $gameState): void
    {
        $this->gameState = $gameState;
    }

    public function response(array $winner = null): array
    {
        return [
            'deck'           => $this->dealer->getDeck(),
            'pot'            => $this->gameState->getPot(),
            'communityCards' => $this->gameState->setCommunityCards()->getCommunityCards(),
            'players'        => $this->playerHandler->handle($this->gameState),
            'winner'         => $winner
        ];
    }

    public function play(GameState $gameState)
    {
        $this->gameState = $gameState;

        return $this->nextStep();
    }

    public function nextStep()
    {
        if ($this->theLastHandWasCompleted()) { return $this->start(); }

        $this->gameState->setPlayers();

        if ($this->theBigBlindIsTheOnlyActivePlayerRemainingPreFlop()) {
            TableSeat::bigBlindWins($this->gameState->handId());

            return $this->showdown();
        }

        if ($this->readyForShowdown() || $this->onePlayerRemainsThatCanContinue()) { return $this->showdown(); }

        if ($this->allActivePlayersCanContinue()) { return $this->inProgress(); }

        return $this->response();
    }

    public function start(GameState $gameState = null, $currentDealer = null) {
        $this->gameState = $this->start->handle($gameState, $currentDealer);

        return $this->response();
    }

    public function inProgress()
    {
        $this->gameState = $this->inProgress->handle($this->gameState);

        return $this->response();
    }

    public function showdown()
    {
        $this->gameState = $this->showdown->handle($this->gameState);

        return $this->response($this->gameState->getWinner());
    }

    protected function readyForShowdown()
    {
        return count($this->gameState->getHandStreets()->content) === count($this->game->streets) &&
            count($this->gameState->getActivePlayers()) ===
            count($this->gameState->getContinuingPlayers());
    }

    protected function onePlayerRemainsThatCanContinue()
    {
        return count($this->gameState->getActivePlayers())
            === count($this->gameState->getContinuingPlayers())
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
