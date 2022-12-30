<?php

namespace Atsmacode\PokerGame\Controllers;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class HandControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $controller = new $requestedName($container);

        return $controller;
    }
}
