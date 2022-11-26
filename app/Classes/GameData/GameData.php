<?php
namespace App\Classes\GameData;

use App\Classes\Database;
use App\Models\HandStreet;
use App\Models\Player;

class GameData extends Database
{
    public function getSeats($tableId)
    {
        $query = sprintf("
            SELECT
                *
            FROM
                table_seats
            WHERE
                table_id = :table_id 
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->bindParam(':table_id', $tableId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(\PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getPlayers($handId)
    {
        $query = sprintf("
            SELECT
                ts.can_continue,
                ts.is_dealer,
                ts.player_id,
                ts.table_id,
                pa.bet_amount,
                pa.active,
                pa.has_acted,
                pa.big_blind,
                pa.small_blind,
                pa.action_id,
                pa.hand_id,
                pa.hand_street_id,
                pa.id AS player_action_id,
                ts.id AS table_seat_id,
                s.amount AS stack,
                a.name AS actionName,
                p.name AS playerName
            FROM
                table_seats AS ts
            LEFT JOIN
                player_actions AS pa ON ts.id = pa.table_seat_id
            LEFT JOIN
                players AS p ON pa.player_id = p.id
            LEFT JOIN
                stacks AS s ON pa.player_id = s.player_id AND ts.table_id = s.table_id
            LEFT JOIN
                actions AS a ON pa.action_id = a.id
            WHERE
                pa.hand_id = :hand_id
            ORDER BY
                ts.id ASC
        ");

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->bindParam(':hand_id', $handId);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch(\PDOException $e) {
            error_log(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    public function getWholeCards(array $players, int $handId): array
    {
        $wholeCards = [];

        foreach ($players as $player) {
            foreach (Player::getWholeCards($handId, $player['player_id']) as $wholeCard) {
                $data = [
                    'player_id'        => $wholeCard['player_id'],
                    'rank'             => $wholeCard['rank'],
                    'rankAbbreviation' => $wholeCard['rankAbbreviation'],
                    'suit'             => $wholeCard['suit'],
                    'suitAbbreviation' => $wholeCard['suitAbbreviation']
                ];

                if (array_key_exists($wholeCard['player_id'], $wholeCards)) {
                    array_push($wholeCards[$wholeCard['player_id']], $data);
                } else {
                    $wholeCards[$wholeCard['player_id']][] = $data;
                }
            }
        }

        return $wholeCards;
    }

    public function getCommunityCards(HandStreet $handStreets): array
    {
        $communityCards = [];

        foreach ($handStreets->collect()->content as $street) {
            foreach ($street->cards()->collect()->content as $streetCard) {
                $communityCards[] = [
                    'rankAbbreviation' => $streetCard->getCard()['rankAbbreviation'],
                    'suit'             => $streetCard->getCard()['suit'],
                    'suitAbbreviation' => $streetCard->getCard()['suitAbbreviation']
                ];
            }
        }

        return $communityCards;
    }
}