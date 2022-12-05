<?php

namespace Atsmacode\PokerGame;

use Doctrine\DBAL\DriverManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class DbalTestFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(PokerGameConfigProvider::class);
        $config         = $configProvider->get();

        return DriverManager::getConnection([
            'dbname'   => $config['db']['test']['database'],
            'user'     => $config['db']['test']['username'],
            'password' => $config['db']['test']['password'],
            'host'     => $config['db']['test']['servername'],
            'driver'   => $config['db']['test']['driver'],
        ]);
    }
}
