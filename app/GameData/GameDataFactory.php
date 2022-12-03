<?php

namespace Atsmacode\PokerGame\GameData;

use Atsmacode\PokerGame\GameData\GameData;
use Atsmacode\PokerGame\PokerGameConfigProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class GameDataFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new GameData(new PokerGameConfigProvider());
    }
}
