<?php

namespace App\Console\Commands;

use PDO;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:build-env',
    description: 'Populate the DB with all resources',
    hidden: false,
    aliases: ['app:build-env']
)]

class BuildEnvironment extends Command
{

    private $methods = [
        'dropDatabase',
        'createDatabase',
        'createRanksTable',
        'createSuitsTable',
        'createCardsTable',
        'seedRanks',
        'seedSuits',
        'seedCards'
    ];
    protected static $defaultName = 'app:build-env';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        [
            'servername' => $this->servername,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database
        ] = require('config/db.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach($this->methods as $method){
            $this->{$method}($output);
        }

        return Command::SUCCESS;

    }

    private function dropDatabase($output)
    {

        $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";

        try {
            $conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Database dropped successfully");
        } catch(PDOException $e) {
            echo $sql . $e->getMessage();
        }
        $conn = null;

        return $this;

    }

    private function createDatabase($output)
    {

        $sql = "CREATE DATABASE `read-right-hands-vanilla`";

        try {
            $conn = new PDO("mysql:host=$this->servername", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Database created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;

        return $this;

    }

    private function createRanksTable($output)
    {

        $sql = "CREATE TABLE ranks (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                ranking INT(2) NOT NULL,
                abbreviation VARCHAR(30) NOT NULL
            )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Ranks table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    private function createSuitsTable($output)
    {

        $sql = "CREATE TABLE suits (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
            abbreviation VARCHAR(30) NOT NULL
        )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Suits table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }
        $conn = null;
    }

    private function createCardsTable($output)
    {

        $sql = "CREATE TABLE cards (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            rank_id INT(6) UNSIGNED NOT NULL,
            suit_id INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (rank_id) REFERENCES ranks(id),
            FOREIGN KEY (suit_id) REFERENCES suits(id)
        )";

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // use exec() because no results are returned
            $conn->exec($sql);
            $output->writeln("Cards table created successfully");
        } catch(PDOException $e) {
            $output->writeln($sql . "<br>" . $e->getMessage());
        }

        $conn = null;
    }

    private function seedRanks($output)
    {

        $ranks = require('config/ranks.php');

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    private function seedSuits($output)
    {

        $suits = require('config/suits.php');

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    private function seedCards($output)
    {

        $ranks = $this->selectRanks($output);
        $suits = $this->selectSuits($output);

        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    private function selectRanks($output)
    {
        $rows = null;

        try {

            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM ranks");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $output->writeln("Ranks selected successfully");

        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }

        $conn = null;

        return $rows;

    }

    private function selectSuits($output)
    {
        $rows = null;

        try {

            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM suits");
            $stmt->execute();

            $rows = $stmt->fetchAll();

            $output->writeln("Suits selected successfully");

        } catch(PDOException $e) {
            $output->writeln($e->getMessage());
        }
        $conn = null;

        return $rows;
    }
}