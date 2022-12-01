<?php

namespace Atsmacode\PokerGame\Models;

use Atsmacode\PokerGame\ConfigProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class TableSeatFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(ConfigProvider::class);
        $config         = $configProvider->get();
        $connection     = $container->get($config['db']['provider']);

        return new TableSeat($connection);
    }
}
