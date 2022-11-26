<?php declare(strict_types=1);

namespace App\Classes\GameState;

use App\Classes\Dealer\Dealer;
use App\Classes\Game\Game;
use App\Classes\GameData\GameData;
use App\Models\Hand;
use App\Models\HandStreet;
use App\Models\PlayerAction;
use App\Models\Table;

class GameState
{
    private array         $deck;
    private array         $communityCards = [];
    private array         $wholeCards = [];
    private ?array        $winner = null;
    private ?PlayerAction $latestAction;
    private Hand          $hand;
    private int           $tableId;
    private int           $handId;
    private array         $seats;
    private ?array        $actions;
    private HandStreet    $handStreets;
    private array         $players;
    private array         $stacks;
    private bool          $newStreet = false;
    private GameData      $gameData;
    private Game          $game;
    private Dealer        $dealer;

    public function __construct(GameData $gameData, Hand $hand = null)
    {
        $this->gameData = $gameData;
        
        if ($hand) {
            $this->initiate($hand);
        }
    }

    public function initiate(Hand $hand)
    {
        $this->hand        = $hand;
        $this->tableId     = $hand->table_id;
        $this->handId      = $hand->id;
        $this->seats       = $this->gameData->getSeats($this->tableId);
        $this->handStreets = $this->hand->streets();
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

    public function updateHandStreets(): self
    {
        $this->handStreets = $this->hand->streets();

        return $this;
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

    public function setCommunityCards(): self
    {
        $this->communityCards = $this->gameData->getCommunityCards($this->getHandStreets());

        return $this;
    }

    public function getCommunityCards(): array
    {
        return $this->communityCards;
    }

    public function setWholeCards(): self
    {
        $this->wholeCards = $this->gameData->getWholeCards($this->getPlayers(), $this->handId);

        return $this;
    }

    public function getWholeCards(): array
    {
        return $this->wholeCards;
    }

    public function setPlayers(): self
    {
        $this->players = $this->gameData->getPlayers($this->handId);

        return $this;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getActivePlayers(): array
    {
        return array_filter($this->players, function($player){
            return 1 === $player['active'];
        });
    }

    public function getContinuingPlayers(): array
    {
        return array_filter($this->players, function($player){
            return 1 === $player['active'] && 1 === $player['can_continue'];
        });
    }

    public function firstActivePlayer()
    {
        $key = array_search(1, array_column($this->players, 'active'));

        if ($key !== false) {
            return $this->players[$key];
        }

        return false;
    }

    public function setWinner(array $winner): void
    {
        $this->winner = $winner;
    }

    public function getWinner(): ?array
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

    public function setNewStreet(): void
    {
        $this->newStreet = true;
    }

    public function isNewStreet(): bool
    {
        return $this->newStreet;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGameDealer(Dealer $dealer): void
    {
        $this->dealer = $dealer;
    }

    public function getGameDealer(): Dealer
    {
        return $this->dealer;
    }
}