<?php

namespace Atsmacode\PokerGame\Database\Seeders;

use Atsmacode\Framework\Dbal\Database;

class SeedPlayers extends Database
{
    public static array $methods = [
        'seed'
    ];

    public function seed()
    {
        $this->createPlayers();
    }

    private function createPlayers()
    {
        $seats = 6;

        try {

            $inserted = 0;

            while($inserted < $seats){

                $seatId = $inserted + 1;
                $name = 'Player ' . $seatId;
                $email = 'player' . $seatId . '@rrh.com';

                $stmt = $this->connection->prepare("INSERT INTO players (name, email) VALUES (:name, :email)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);

                $stmt->execute();

                $playerId = $this->connection->lastInsertId();

                $this->addPlayerToSeat($playerId, $seatId);

                $inserted++;
            }
        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }
        $this->connection = null;

        return $this;
    }

    private function addPlayerToSeat($playerId, $seatId)
    {
        try {
            $stmt = $this->connection->prepare("
                    UPDATE table_seats SET player_id = :playerId
                    WHERE id = :seatId
                ");
            $stmt->bindParam(':playerId', $playerId);
            $stmt->bindParam(':seatId', $seatId);

            $stmt->execute();

        } catch(\PDOException $e) {
            error_log($e->getMessage());
        }

        return $this;
    }
}
