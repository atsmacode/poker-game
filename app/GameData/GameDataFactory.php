<?php

namespace Atsmacode\PokerGame\GameData;

use Atsmacode\PokerGame\GameData\GameData;
use Atsmacode\PokerGame\Models\Hand;
use Atsmacode\PokerGame\Models\HandStreetCard;
use Atsmacode\PokerGame\Models\Player;
use Atsmacode\PokerGame\Models\Table;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class GameDataFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $handModel           = $container->get(Hand::class);
        $handStreetCardModel = $container->get(HandStreetCard::class);
        $tableModel          = $container->get(Table::class);
        $playerModel         = $container->get(Player::class);

        return new GameData(
            $handModel,
            $tableModel,
            $handStreetCardModel,
            $playerModel
        );
    }
}
