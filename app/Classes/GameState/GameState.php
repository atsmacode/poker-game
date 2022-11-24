<?php declare(strict_types=1);

namespace App\Classes\GameState;

use App\Classes\GameData\GameData;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Table;

class GameState implements GameStateInterface
{
    private array         $deck;
    private array         $communityCards;
    private array         $winner;
    private ?PlayerAction $latestAction;
    private Hand          $hand;
    private int           $tableId;
    private int           $handId;
    private array         $seats;
    private ?array        $actions;
    private HandStreet    $handStreets;
    private array         $players;
    private array         $stacks;

    public function __construct(Hand $hand = null)
    {
        if ($hand) {
            $this->initiate($hand);
        }
    }

    public function initiate(Hand $hand)
    {
        $this->hand        = $hand;
        $this->tableId     = $hand->table_id;
        $this->handId      = $hand->id;
        $this->seats       = GameData::getSeats($this->tableId);
        $this->handStreets = $this->hand->streets();
    }

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

    public function getSeat(int $seatId)
    {
        $key = array_search($seatId, array_column($this->seats, 'id'));

        if ($key !== false) {
            return $this->seats[$key];
        }

        return false;
    }

    public function getDealer()
    {
        $key = array_search(1, array_column($this->seats, 'is_dealer'));

        if ($key !== false) {
            return $this->seats[$key];
        }

        return false;
    }

    public function getSeatAction(int $seatId)
    {
        $key = array_search($seatId, array_column($this->actions, 'table_seat_id'));

        if ($key !== false) {
            return $this->actions[$key];
        }

        return false;
    }

    public function setHand(Hand $hand): void
    {
        $this->hand = $hand;
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function tableId(): int
    {
        return $this->tableId;
    }

    public function handId(): int
    {
        return $this->handId;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }

    public function getHandStreets(): HandStreet
    {
        return $this->handStreets;
    }

    public function getUpdatedHandStreets(): HandStreet
    {
        return $this->hand->streets();
    }

    public function incrementedHandStreets(): int
    {
        return count($this->handStreets->content) + 1;
    }

    public function handStreetCount(): int
    {
        return count($this->handStreets->content);
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

    public function getPot(): int
    {
        $pot = $this->hand->pot();

        return isset($pot->amount) ? $pot->amount : 0;
    }

    public function setCommunityCards(array $communityCards): void
    {
        $this->communityCards = $communityCards;
    }

    public function getCommunityCards(): array
    {
        return $this->communityCards;
    }

    public function setPlayers(): void
    {
        $this->players = GameData::getPlayers($this->handId);
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

    public function setStacks(array $stacks): void
    {
        $this->stacks = $stacks;
    }

    public function getStacks(): array
    {
        return $this->stacks;
    }
}