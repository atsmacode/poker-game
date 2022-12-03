<?php

namespace Atsmacode\PokerGame;

use Atsmacode\Framework\ConfigProvider;
use Atsmacode\PokerGame\PokerGameConfig;
use Laminas\ConfigAggregator\ConfigAggregator;

class PokerGameConfigProvider extends ConfigProvider
{
    public function get()
    {
        $aggregator = new ConfigAggregator([
            PokerGameConfig::class
        ]);

        return $aggregator->getMergedConfig(); 
    }
}
