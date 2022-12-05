<?php

namespace Atsmacode\PokerGame;

use Atsmacode\Framework\ConfigProvider;
use Doctrine\DBAL\DriverManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class DbalLiveFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(PokerGameConfigProvider::class);
        $config         = $configProvider->get();

        return DriverManager::getConnection([
            'dbname'   => $config['db']['live']['database'],
            'user'     => $config['db']['live']['username'],
            'password' => $config['db']['live']['password'],
            'host'     => $config['db']['live']['servername'],
            'driver'   => $config['db']['live']['driver'],
        ]);
    }
}
