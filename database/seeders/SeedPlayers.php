<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;

class SeedPlayers
{

    public static array $methods = [
        'seed'
    ];

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function seed()
    {
        $this->createPlayers();
    }

    private function createPlayers()
    {

        $seats = 6;

        try {
            $conn = new CustomPDO(true);

            $inserted = 0;

            while($inserted < $seats){

                $seatId = $inserted + 1;
                $username = 'Player ' . $seatId;
                $email = 'player' . $seatId . '@rrh.com';

                $stmt = $conn->prepare("INSERT INTO players (username, email) VALUES (:username, :email)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);

                $stmt->execute();

                $playerId = $conn->lastInsertId();

                $this->addPlayerToSeat($playerId, $seatId);

                $inserted++;
            }

            $this->output->writeln("Players seeded successfully");

        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;

        return $this;

    }

    private function addPlayerToSeat($playerId, $seatId)
    {

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    UPDATE table_seats SET player_id = :playerId
                    WHERE id = :seatId
                ");
            $stmt->bindParam(':playerId', $playerId);
            $stmt->bindParam(':seatId', $seatId);

            $stmt->execute();

        } catch(PDOException $e) {
            $this->output->writeln($e->getMessage());

        }
        $conn = null;

        return $this;

    }

}