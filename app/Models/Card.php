<?php

namespace App\Models;

class Card
{
    public string $rank;
    public string $suit;
    public int $suit_id;
    public int $rank_id;
    public int $ranking;
    public $content;

    public function __construct(array $card = null)
    {
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
    }

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
}
