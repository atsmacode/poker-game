<?php declare(strict_types=1);

namespace App\Classes\GameState;

use App\Models\Hand;
use App\Models\PlayerAction;

class GameState implements GameStateInterface
{
    private array         $deck;
    private int           $pot;
    private array         $communityCards;
    private array         $players;
    private array         $winner;
    private ?PlayerAction $latestAction;
    private Hand          $hand;

    public function state(): array
    {
        return [
            'deck'           => $this->deck,
            'pot'            => $this->pot,
            'communityCards' => $this->communityCards,
            'players'        => $this->players,
            'winner'         => $this->winner
        ];
    }

    public function setHand(Hand $hand): void
    {
        $this->hand = $hand;
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }

    public function setLatestAction(PlayerAction $playerAction): void
    {
        $this->latestAction = $playerAction;
    }

    public function getLatestAction(): ?PlayerAction
    {
        return $this->latestAction ?? null;
    }

    public function setDeck(array $deck): void
    {
        $this->deck = $deck;
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function setPot(int $potAmount): void
    {
        $this->pot = $potAmount;
    }

    public function getPot(): int
    {
        return $this->pot;
    }

    public function setCommunityCards(array $communityCards): void
    {
        $this->communityCards = $communityCards;
    }

    public function getCommunityCards(): array
    {
        return $this->communityCards;
    }

    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function setWinner(array $winner): void
    {
        $this->winner = $winner;
    }

    public function getWinner(): array
    {
        return $this->winner;
    }
}