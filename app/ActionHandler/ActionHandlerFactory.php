<?php

namespace Atsmacode\PokerGame\ActionHandler;

use Atsmacode\PokerGame\GameState\GameState;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class ActionHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ActionHandler(
            isset($options['gameState']) ? $options['gameState'] : $container->get(GameState::class),
        );
    }
}
