<?php

namespace Database\Seeders;

use App\Classes\Database;
use App\Helpers\QueryHelper;

class SeedCards extends Database
{
    public static array $methods = [
        'seedRanks',
        'seedSuits',
        'seedCards'
    ];
    public function seedRanks()
    {
        $ranks = require('config/ranks.php');

        try {
            $stmt = $this->connection->prepare("INSERT INTO ranks (name, abbreviation, ranking) VALUES (:name, :abbreviation, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);
            $stmt->bindParam(':ranking', $ranking);

            foreach($ranks as $rank) {
                $name = $rank['name'];
                $abbreviation = $rank['abbreviation'];
                $ranking = $rank['ranking'];
                $stmt->execute();
            }
        } catch(PDOException $e) {
            error_log($e->getMessage());

        }
        $this->connection = null;
    }

    public function seedSuits()
    {
        $suits = require('config/suits.php');

        try {
            $stmt = $this->connection->prepare("INSERT INTO suits (name, abbreviation) VALUES (:name, :abbreviation)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);

            foreach($suits as $suit) {
                $name = $suit['name'];
                $abbreviation = $suit['abbreviation'];
                $stmt->execute();
            }
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }
        $this->connection = null;
    }

    public function seedCards()
    {
        $ranks = QueryHelper::selectRanks();
        $suits = QueryHelper::selectSuits();

        try {
            $stmt = $this->connection->prepare("INSERT INTO cards (rank_id, suit_id) VALUES (:rank_id, :suit_id)");
            $stmt->bindParam(':rank_id', $rank_id);
            $stmt->bindParam(':suit_id', $suit_id);

            foreach($suits as $suit){
                foreach($ranks as $rank){
                    $rank_id = $rank['id'];
                    $suit_id = $suit['id'];
                    $stmt->execute();
                }
            }
        } catch(PDOException $e) {
            error_log($e->getMessage());
        }

        $this->connection = null;
    }
}
