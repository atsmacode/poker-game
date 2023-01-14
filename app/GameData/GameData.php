<?php

namespace Atsmacode\PokerGame\GameData;

use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Atsmacode\PokerGame\Models\TableSeat;

/**
 * Responsible for providing the baseline data a Hand needs throught the process.
 */
class GameData
{
    public function __construct(
        private Hand           $handModel,
        private Table          $tableModel,
        private HandStreetCard $handStreetCardModel,
        private Player         $playerModel,
        private TableSeat      $tableSeatModel
    ) {}

    public function getSeats(int $tableId): array
    {
        return $this->tableModel->getSeats($tableId);
    }

    public function getPlayers(int $handId): array
    {
        return $this->handModel->getPlayers($handId);
    }

    public function getWholeCards(array $players, int $handId): array
    {
        $wholeCards = [];

        foreach ($players as $player) {
            foreach ($this->playerModel->getWholeCards($handId, $player['player_id']) as $wholeCard) {
                if (array_key_exists($wholeCard['player_id'], $wholeCards)) {
                    array_push($wholeCards[$wholeCard['player_id']], $wholeCard);
                } else {
                    $wholeCards[$wholeCard['player_id']][] = $wholeCard;
                }
            }
        }

        return $wholeCards;
    }

    public function getCommunityCards(int $handId): array
    {
        return $this->handModel->getCommunityCards($handId);
    }

    public function getBigBlind(int $handId): array
    {
        return $this->tableSeatModel->getBigBlind($handId);
    }
}
