<?php

namespace Atsmacode\PokerGame\Dealer;

use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\WholeCard;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class PokerDealerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $stackModel = $container->get(WholeCard::class);
        $potModel   = $container->get(HandStreetCard::class);

        return new PokerDealer(
            $stackModel,
            $potModel
        );
    }
}
