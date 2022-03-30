<?php

namespace Database\Seeders;

use App\Classes\CustomPDO;
use App\Helpers\QueryHelper;

class SeedCards
{

    public static array $methods = [
        'seedRanks',
        'seedSuits',
        'seedCards'
    ];

    public function seedRanks($output)
    {

        $ranks = require('config/ranks.php');

        try {
            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("INSERT INTO ranks (name, abbreviation, ranking) VALUES (:name, :abbreviation, :ranking)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);
            $stmt->bindParam(':ranking', $ranking);

            foreach($ranks as $rank) {
                $name = $rank['name'];
                $abbreviation = $rank['abbreviation'];
                $ranking = $rank['ranking'];
                $stmt->execute();
            }
            $output->writeln("Ranks seeded successfully");
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());

        }
        $conn = null;
    }

    public function seedSuits($output)
    {

        $suits = require('config/suits.php');

        try {
            $conn = new CustomPDO(true);
            $stmt = $conn->prepare("INSERT INTO suits (name, abbreviation) VALUES (:name, :abbreviation)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);

            foreach($suits as $suit) {
                $name = $suit['name'];
                $abbreviation = $suit['abbreviation'];
                $stmt->execute();
            }
            $output->writeln("Suits seeded successfully");
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }
        $conn = null;
    }

    public function seedCards($output)
    {

        $ranks = QueryHelper::selectRanks($output);
        $suits = QueryHelper::selectSuits($output);

        try {
            $conn = new CustomPDO(true);
            $stmt = $conn->prepare("INSERT INTO cards (rank_id, suit_id) VALUES (:rank_id, :suit_id)");
            $stmt->bindParam(':rank_id', $rank_id);
            $stmt->bindParam(':suit_id', $suit_id);

            foreach($suits as $suit){
                foreach($ranks as $rank){
                    $rank_id = $rank['id'];
                    $suit_id = $suit['id'];
                    $stmt->execute();
                }
            }

            $output->writeln("Cards seeded successfully");
        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }

        $conn = null;

    }

}