<?php

namespace App\Classes;

class PotLimitHoldEm implements Game
{

    public array $streets;
    public string $limit;

    public function __construct()
    {
        $this->streets = [
            [
                'name' => 'Pre-flop',
                'whole_cards' => 2,
                'community_cards' => 0
            ],
            [
                'name' => 'Flop',
                'whole_cards' => 0,
                'community_cards' => 3
            ],
            [
                'name' => 'Turn',
                'whole_cards' => 0,
                'community_cards' => 1
            ],
            [
                'name' => 'River',
                'whole_cards' => 0,
                'community_cards' => 1
            ]
        ];
        $this->limit = 'pot';
    }

}
