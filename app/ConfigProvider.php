<?php

namespace Atsmacode\PokerGame;

use Laminas\ConfigAggregator\ConfigAggregator;

class ConfigProvider
{
    public function get()
    {
        $aggregator = new ConfigAggregator([
            DbConfig::class
        ]);

        return $aggregator->getMergedConfig(); 
    }
}
