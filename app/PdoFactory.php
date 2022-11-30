<?php

namespace Atsmacode\PokerGame;

use Laminas\ServiceManager\Factory\FactoryInterface;
use PDO;
use Psr\Container\ContainerInterface;

class PdoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $configProvider = $container->get(ConfigProvider::class);
        $config         = $configProvider->get();
        $env            = 'live';

        if (isset($GLOBALS['dev'])) {
            $env = 'test';
        }

        return new PDO(
            'mysql:host=' . $config['db'][$env]['servername'] . ';dbname=' . $config['db'][$env]['database'],
            $config['db'][$env]['username'],
            $config['db'][$env]['password'],
            array(
            PDO::ATTR_PERSISTENT => true
        ));
    }
}
