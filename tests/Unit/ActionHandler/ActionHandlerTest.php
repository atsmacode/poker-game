<?php

namespace Atsmacode\PokerGame\Tests\Unit\ActionHandler;

use Atsmacode\PokerGame\ActionHandler\ActionHandler;
use Atsmacode\PokerGame\Constants\Action;
use Atsmacode\PokerGame\GameState\GameState;
use Atsmacode\PokerGame\Models\Pot;
use Atsmacode\PokerGame\Tests\BaseTest;
use Atsmacode\PokerGame\Tests\HasGamePlay;

class ActionHandlerTest extends BaseTest
{
    use HasGamePlay;

    public function setUp(): void
    {
        parent::setUp();

        $this->isThreeHanded();

        $this->potModel      = $this->container->build(Pot::class);
        $this->actionHandler = $this->container->build(
            ActionHandler::class,
            ['gameState' => $this->gameState
        ]);
    }

    /** @test */
    public function handleReturnsInstanceOfGameState()
    {
        $this->gamePlay->start();

        $response = $this->actionHandler->handle(
            $this->hand,
            $this->player1->getId(),
            $this->tableSeatOne->getId(),
            1,
            50,
            Action::CALL_ID,
            1,
            1000
        );

        $this->assertInstanceOf(GameState::class, $response);
    }
}
