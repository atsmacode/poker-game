<?php

namespace Tests\Unit;

use App\Classes\GamePlay;
use App\Models\Hand;
use PHPUnit\Framework\TestCase;

class GamePlayPotTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $this->gamePlay = new GamePlay(Hand::create(['table_id' => 1]));
    }

    /**
     * @test
     * @return void
     */
    public function with_blinds_25_and_50_the_pot_size_will_be_75_once_the_hand_is_started()
    {
        $this->gamePlay->start();

        $this->assertEquals(75, $this->gamePlay->hand->pot()->amount);
    }

}
