<?php

namespace Atsmacode\PokerGame;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

class LoggerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = (new PokerGameConfigProvider())->get();
        $logger = new Logger('poker-game');

        $logger->pushHandler(new StreamHandler($config['logger']['path']));

        return $logger;
    }
}
