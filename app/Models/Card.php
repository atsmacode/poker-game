<?php

namespace App\Models;

use App\Traits\Connect;
use PDO;
use PDOException;

class Card extends Model
{

    use Connect, Collection;

    public string $rank;
    public string $suit;
    public int $suit_id;
    public int $rank_id;
    public int $ranking;
    public $content;

    public function __construct(array $card = null)
    {
        //var_dump($card);
        if ($card) {
            $merged = array_merge(
                $card[0],
                $card[1]
            );
            $merged['id'] = $card['id'];
    
            $this->rank             = $merged['rank'];
            $this->suit             = $merged['suit'];
            $this->suit_id          = $merged['suit_id'];
            $this->rank_id          = $merged['rank_id'];
            $this->ranking          = $merged['ranking'];
            $this->id               = $merged['id'];
            $this->rankAbbreviation = $merged['rankAbbreviation'];
            $this->suitAbbreviation = $merged['suitAbbreviation'];
        }

        parent::__construct();
        $this->setCredentials();
        // $this->selectedRank = array_key_exists('rank', $merged)  ? $merged['rank'] : null;
        // $this->selectedSuit = array_key_exists('suit', $merged)  ? $merged['suit'] : null;
        // $this->id = array_key_exists('id', $data) ? $data['id'] : null;
        // $this->select();
    }

    // public function __serialize(): array
    // {
    //     parent::__serialize();

    //     return (array) $this;
    // }

    // public function __unserialize(array $data): void
    // {
    //     parent::__unserialize($data);

    //     $this->rank             = $data['rank'];
    //     $this->suit             = $data['suit'];
    //     $this->suit_id          = $data['suit_id'];
    //     $this->rank_id          = $data['rank_id'];
    //     $this->ranking          = $data['ranking'];
    //     $this->id               = $data['id'];
    //     $this->rankAbbreviation = $data['rankAbbreviation'];
    //     $this->suitAbbreviation = $data['suitAbbreviation'];
    // }

    // public function select()
    // {
    //     if($this->selectedRank && $this->selectedSuit || $this->id){
    //         $this->getSelected();
    //     }
    // }

    protected function setModelProperties($rows)
    {
        $this->rank             = $rows['rank'];
        $this->suit             = $rows['suit'];
        $this->suit_id          = $rows['suit_id'];
        $this->rank_id          = $rows['rank_id'];
        $this->ranking          = $rows['ranking'];
        $this->id               = $rows['id'];
        $this->rankAbbreviation = $rows['rankAbbreviation'];
        $this->suitAbbreviation = $rows['suitAbbreviation'];

        return $this;

    }

    // private function getByNames()
    // {
    //     try {

    //         $stmt = $this->connection->prepare("
    //                 SELECT 
    //                     c.*,
    //                     r.name AS 'rank',
    //                     r.abbreviation AS rankAbbreviation,
    //                     s.name AS suit,
    //                     s.abbreviation AS suitAbbreviation,
    //                     r.ranking AS ranking 
    //                 FROM 
    //                     cards c
    //                 LEFT OUTER JOIN 
    //                     ranks r ON c.rank_id = r.id
    //                 LEFT OUTER JOIN 
    //                     suits s ON c.suit_id = s.id
    //                 WHERE 
    //                     r.name = '{$this->selectedRank}' AND s.name = '{$this->selectedSuit}'
    //             ");
    //         $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //         $stmt->execute();

    //         $rows = $stmt->fetchAll();

    //     } catch(PDOException $e) {
    //         echo $e->getMessage();
    //         return null;
    //     }

    //     return $rows;
    // }

    // public static function getById(int $cardId)
    // {
    //     return (new static())->getByIdQuery($cardId);
    // }

    // private function getByIdQuery(int $cardId)
    // {
    //     try {

    //         $stmt = $this->connection->prepare("
    //                 SELECT
    //                     c.*,
    //                     r.name AS 'rank',
    //                     r.abbreviation AS rankAbbreviation,
    //                     s.name AS suit,
    //                     s.abbreviation AS suitAbbreviation,
    //                     r.ranking AS ranking 
    //                 FROM 
    //                     cards c
    //                 LEFT OUTER JOIN 
    //                     ranks r ON c.rank_id = r.id
    //                 LEFT OUTER JOIN 
    //                     suits s ON c.suit_id = s.id
    //                 WHERE 
    //                     c.id = {$cardId}
    //             ");
    //         $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //         $stmt->execute();

    //         $row = $stmt->fetch();

    //     } catch(PDOException $e) {
    //         echo $e->getMessage();
    //         return null;
    //     }

    //     return $this->getSelected($row);
    // }

    // private function getById()
    // {
    //     try {

    //         $stmt = $this->connection->prepare("
    //                 SELECT
    //                     c.*,
    //                     r.name AS 'rank',
    //                     r.abbreviation AS rankAbbreviation,
    //                     s.name AS suit,
    //                     s.abbreviation AS suitAbbreviation,
    //                     r.ranking AS ranking 
    //                 FROM 
    //                     cards c
    //                 LEFT OUTER JOIN 
    //                     ranks r ON c.rank_id = r.id
    //                 LEFT OUTER JOIN 
    //                     suits s ON c.suit_id = s.id
    //                 WHERE 
    //                     c.id = {$this->id}
    //             ");
    //         $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //         $stmt->execute();

    //         $rows = $stmt->fetchAll();

    //     } catch(PDOException $e) {
    //         echo $e->getMessage();
    //         return null;
    //     }

    //     return $rows;
    // }
}
