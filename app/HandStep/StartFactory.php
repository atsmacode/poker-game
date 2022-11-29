<?php

namespace Atsmacode\PokerGame\HandStep;

use Atsmacode\PokerGame\Models\Street;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class StartFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $street = $container->get(Street::class);

        return new Start($street);
    }
}
