<?php

namespace App\Classes;

use PDO;
use PDOException;

class Deck
{

    use Connect;

    public $cards;

    public function __construct()
    {
        $this->setCredentials();
        $this->cards = $this->selectCards();
    }

    private function selectCards()
    {
        $rows = null;

        try {

            $conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM cards");
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;

        return $rows;
    }
}