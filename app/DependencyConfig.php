<?php

namespace Atsmacode\PokerGame;

class DependencyConfig
{
    public function get()
    {
        return [
            'dependencies' => [
                'invokables' => [
                    \Atsmacode\PokerGame\PlayerHandler\PlayerHandler::class,
                    \Atsmacode\PokerGame\Game\PotLimitHoldEm::class,
                    \Atsmacode\PokerGame\Game\PotLimitOmaha::class,
                ],
                'factories' => [
                    \Atsmacode\PokerGame\PokerGameConfigProvider::class
                        => \Atsmacode\PokerGame\PokerGameRelConfigProviderFactory::class,
                    \Atsmacode\Framework\Database\ConnectionInterface::class
                        => \Atsmacode\PokerGame\Database\DbalLiveFactory::class,
                    \PDO::class
                        => \Atsmacode\PokerGame\Database\PdoLiveFactory::class,
                    \Atsmacode\PokerGame\Dealer\PokerDealer::class
                        => \Atsmacode\PokerGame\Dealer\PokerDealerFactory::class,
                    \Atsmacode\PokerGame\Models\Street::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\Table::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\Hand::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\Player::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\TableSeat::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\HandStreet::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\PlayerAction::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\Stack::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\Pot::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\PlayerActionLog::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\WholeCard::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\HandStreetCard::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\Models\HandType::class
                        => \Atsmacode\Framework\Models\ModelFactory::class,
                    \Atsmacode\PokerGame\PlayerHandler\PlayerHandler::class
                        => \Atsmacode\PokerGame\PlayerHandler\PlayerHandlerFactory::class,
                    \Atsmacode\PokerGame\BetHandler\BetHandler::class
                        => \Atsmacode\PokerGame\BetHandler\BetHandlerFactory::class,
                    \Atsmacode\PokerGame\PotHandler\PotHandler::class
                        => \Atsmacode\PokerGame\PotHandler\PotHandlerFactory::class,
                    \Atsmacode\PokerGame\HandStep\NewStreet::class
                        => \Atsmacode\PokerGame\HandStep\NewStreetFactory::class,
                    \Atsmacode\PokerGame\HandStep\Start::class
                        => \Atsmacode\PokerGame\HandStep\StartFactory::class,
                    \Atsmacode\PokerGame\HandStep\Showdown::class
                        => \Atsmacode\PokerGame\HandStep\ShowdownFactory::class,
                    \Atsmacode\PokerGame\Factory\PlayerActionFactory::class
                        => \Atsmacode\PokerGame\Factory\PlayerActionFactoryFactory::class,
                    \Atsmacode\PokerGame\GameData\GameData::class
                        => \Atsmacode\PokerGame\GameData\GameDataFactory::class,
                    \Atsmacode\PokerGame\GamePlay\GamePlay::class 
                        => \Atsmacode\PokerGame\GamePlay\GamePlayFactory::class,
                    \Atsmacode\PokerGame\GameState\GameState::class 
                        => \Atsmacode\PokerGame\GameState\GameStateFactory::class,
                    \Atsmacode\PokerGame\ActionHandler\ActionHandler::class
                        => \Atsmacode\PokerGame\ActionHandler\ActionHandlerFactory::class,
                    \Atsmacode\PokerGame\Controllers\PotLimitHoldEm\HandController::class
                        => \Atsmacode\PokerGame\Controllers\HandControllerFactory::class,
                    \Atsmacode\PokerGame\Controllers\PotLimitOmaha\HandController::class
                        => \Atsmacode\PokerGame\Controllers\HandControllerFactory::class,
                    \Atsmacode\PokerGame\Controllers\PotLimitHoldEm\PlayerActionController::class
                        => \Atsmacode\PokerGame\Controllers\PlayerActionControllerFactory::class,
                    \Atsmacode\PokerGame\Controllers\PotLimitOmaha\PlayerActionController::class
                        => \Atsmacode\PokerGame\Controllers\PlayerActionControllerFactory::class
                ],
            ],
        ];
    }
}
