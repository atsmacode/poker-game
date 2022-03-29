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
        return (new Card())->content;
    }
}