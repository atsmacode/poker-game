<?php 

namespace Tests\Feature;

use App\Models\HandStreet;
use App\Models\Street;

trait HasStreets
{
    protected function setFlop()
    {
        $flop = HandStreet::create([
            'street_id' => Street::find(['name' => 'Flop'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $flop,
            $this->gamePlay->game->streets[1]['community_cards']
        );
    }

    protected function setTurn()
    {
        $turn = HandStreet::create([
            'street_id' => Street::find(['name' => 'Turn'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $turn,
            $this->gamePlay->game->streets[2]['community_cards']
        );
    }

    protected function setRiver()
    {
        $river = HandStreet::create([
            'street_id' => Street::find(['name' => 'River'])->id,
            'hand_id' => $this->gamePlay->hand->id
        ]);

        $this->gamePlay->dealer->dealStreetCards(
            $river,
            $this->gamePlay->game->streets[3]['community_cards']
        );
    }
}