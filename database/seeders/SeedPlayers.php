<?php

namespace Database\Seeders;

use App\Classes\Database;

class SeedPlayers extends Database
{

    public static array $methods = [
        'seed'
    ];

    public function seed($output)
    {
        $this->createPlayers($output);
    }

    private function createPlayers($output)
    {

        $seats = 6;

        try {

            $inserted = 0;

            while($inserted < $seats){

                $seatId = $inserted + 1;
                $username = 'Player ' . $seatId;
                $email = 'player' . $seatId . '@rrh.com';

                $stmt = $this->connection->prepare("INSERT INTO players (username, email) VALUES (:username, :email)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);

                $stmt->execute();

                $playerId = $this->connection->lastInsertId();

                $this->addPlayerToSeat($playerId, $seatId);

                $inserted++;
            }

            $output->writeln("Players seeded successfully");

        } catch(PDOException $e) {
            $output->writeln($e->getMessage());

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

        } catch(PDOException $e) {
            echo $e->getMessage();

        }

        return $this;

    }

}