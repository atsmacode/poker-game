<?php

namespace Atsmacode\PokerGame\Classes\GameState;

use Atsmacode\PokerGame\Classes\GameData\GameData;
use Atsmacode\PokerGame\Classes\GameState\GameState;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class GameStateFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new GameState(
            $container->get(GameData::class),
            isset($options['hand']) ? $options['hand'] : null
        );
    }
}
