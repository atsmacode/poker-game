<?php

namespace Atsmacode\PokerGame;

use Doctrine\DBAL\DriverManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class DbalFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(ConfigProvider::class);
        $config         = $configProvider->get();
        $env            = 'live';

        if (isset($GLOBALS['dev'])) {
            $env = 'test';
        }

        return DriverManager::getConnection([
            'dbname'   => $config['db'][$env]['database'],
            'user'     => $config['db'][$env]['username'],
            'password' => $config['db'][$env]['password'],
            'host'     => $config['db'][$env]['servername'],
            'driver'   => $config['db'][$env]['driver'],
        ]);
    }
}
