<?php

namespace App\Models;

use App\Classes\CustomPDO;
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

    public function __construct(array $data = null)
    {
        $this->setCredentials();
        $this->selectedRank = array_key_exists('rank', $data)  ? $data['rank'] : null;
        $this->selectedSuit = array_key_exists('suit', $data)  ? $data['suit'] : null;
        $this->id = array_key_exists('id', $data) ? $data['id'] : null;
        $this->select();
    }

    public function select()
    {
        if($this->selectedRank && $this->selectedSuit){
            $this->getSelected();
        }
    }

    protected function getSelected($column = null, $value = null)
    {
        $rows = $this->id ? $this->getById() : $this->getByNames();

        $result = array_shift($rows);
        $this->content = $result;

        $this->rank = $result['rank'];
        $this->suit = $result['suit'];
        $this->suit_id = $result['suit_id'];
        $this->rank_id = $result['rank_id'];
        $this->ranking = $result['ranking'];
        $this->id = $result['id'];

    }

    private function getByNames()
    {
        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT c.*, r.name as rank, s.name as suit, r.ranking as ranking FROM cards c
                    LEFT OUTER JOIN ranks r ON c.rank_id = r.id
                    LEFT OUTER JOIN suits s ON c.suit_id = s.id
                    WHERE r.name = '{$this->selectedRank}' AND s.name = '{$this->selectedSuit}'
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
            return null;
        }

        return $rows;
    }

    private function getById()
    {
        try {

            $conn = new CustomPDO(true);

            $stmt = $conn->prepare("
                    SELECT c.*, r.name as rank, s.name as suit, r.ranking as ranking FROM cards c
                    LEFT OUTER JOIN ranks r ON c.rank_id = r.id
                    LEFT OUTER JOIN suits s ON c.suit_id = s.id
                    WHERE c.id = {$this->id}
                ");
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $rows = $stmt->fetchAll();

        } catch(PDOException $e) {
            echo $e->getMessage();
            return null;
        }

        return $rows;
    }

}