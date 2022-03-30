<?php

namespace App\Models;

use App\Classes\CustomPDO;
use App\Traits\Connect;
use PDO;
use PDOException;

class Card extends Model
{

    use Connect;

    public string $rank;
    public string $suit;
    public int $suit_id;
    public int $rank_id;
    public int $ranking;
    public $content;

    public function __construct(string $rank = null, string $suit = null)
    {
        $this->setCredentials();
        $this->selectedRank = $rank;
        $this->selectedSuit = $suit;
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
        $rows = null;

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
        }

        $conn = null;

        $result = array_shift($rows);
        $this->content = $result;

        $this->rank = $result['rank'];
        $this->suit = $result['suit'];
        $this->suit_id = $result['suit_id'];
        $this->rank_id = $result['rank_id'];
        $this->ranking = $result['ranking'];

    }

}