<?php

namespace App\Classes;

use PDO;
use PDOException;

class Dealer
{

    public $deck;
    public $card;

    public function __construct()
    {
        [
            'servername' => $this->servername,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database
        ] = require('config/db.php');
    }

    public function setDeck()
    {

        $this->deck = $this->selectCards();

        return $this;
    }

    public function getDeck()
    {
        return $this->deck;
    }

    public function shuffle()
    {
        shuffle($this->deck);

        return $this;
    }

    public function pickCard($rank = null, $suit = null)
    {

        if($rank === null && $suit === null){
            $this->card = array_shift($this->deck);
            return $this;
        }

        $this->card = array_values(array_filter($this->deck, function($key, $value) use($rank, $suit){
            return $value->rank_id === $rank->id && $value->suit_id === $suit->id;
        }));

        $card = $this->card;

        array_values(array_filter($this->deck, function($key, $value) use($card){
            return $value !== $card;
        }));

        return $this;
    }

    public function getCard()
    {
        return $this->card;
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

    /*

    public function dealTo($players, $cardCount, $hand = null)
    {
        if($players instanceof Player){

            $dealtCards = 0;
            while($dealtCards < $cardCount){
                $players->wholeCards()->create([
                    'card_id' => $this->pickCard()->getCard()->id,
                    'hand_id' => $hand ? $hand->id : null
                ]);
                $dealtCards++;
            }

            return $this;
        }

        foreach($players as $player){

            $dealtCards = 0;
            while($dealtCards < $cardCount){
                $player->wholeCards()->create([
                    'card_id' => $this->pickCard()->getCard()->id,
                    'hand_id' => $hand ? $hand->id : null
                ]);
                $dealtCards++;
            }

        }

        return $this;

    }

    public function dealCardTo($player)
    {

        $player->wholeCards()->create([
            'card_id' => $this->getCard()->id
        ]);

        return $this;

    }

    public function dealStreetCards($handStreet, $cardCount)
    {

        $dealtCards = 0;

        while($dealtCards < $cardCount){

            $cardId = is_object($this->pickCard()->getCard()) ? $this->pickCard()->getCard()->id : $this->pickCard()->getCard()['id'];

            HandStreetCard::create([
                'card_id' => $cardId,
                'hand_street_id' => $handStreet->id
            ]);

            $dealtCards++;

        }

        return $this;

    }

    public function dealThisStreetCard($rank, $suit, $handStreet)
    {

        $cardId = $this->pickCard($rank, $suit)->getCard()->id;

        HandStreetCard::create([
            'card_id' => $cardId,
            'hand_street_id' => $handStreet->id
        ]);

        return $this;

    }*/
}
