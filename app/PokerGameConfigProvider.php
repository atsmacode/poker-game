<?php

namespace Atsmacode\PokerGame;

use Atsmacode\Framework\ConfigProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

class PokerGameConfigProvider extends ConfigProvider
{
    public function get()
    {
        $aggregator = new ConfigAggregator([
            ConfigDb::class,
            DependencyConfig::class
        ]);

        return $aggregator->getMergedConfig(); 
    }
}
