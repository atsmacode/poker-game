<?php

namespace Atsmacode\PokerGame\Classes\GamePlay;

use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Atsmacode\PokerGame\Classes\HandStep\NewStreet;
use Atsmacode\PokerGame\Classes\HandStep\Showdown;
use Atsmacode\PokerGame\Classes\HandStep\Start;
use Atsmacode\PokerGame\Classes\PlayerHandler\PlayerHandler;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Responsible for deciding what happens next in a hand and 
 * providing the response to the front-end application.
 */
class GamePlayFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $deck      = isset($options['deck']) ? $options['deck'] : null;
        $gameState = isset($options['gameState']) ? $options['gameState'] : new GameState(
            $container->get(GameData::class),
            isset($options['hand']) ? $options['hand'] : null
        );

        return new GamePlay(
            $gameState,
            $options['game'],
            $container->get(Start::class),
            $container->get(NewStreet::class),
            $container->get(Showdown::class),
            $container->get(PlayerHandler::class),
            $deck
        );
    }
}
