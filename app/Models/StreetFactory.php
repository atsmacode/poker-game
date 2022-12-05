<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\PokerGameConfigProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class StreetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(PokerGameConfigProvider::class);
        $config         = $configProvider->get();
        $connection     = $container->get($config['db']['provider']);

        return new Street($connection);
    }
}
