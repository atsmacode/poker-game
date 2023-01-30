<?php

use Atsmacode\PokerGame\PokerGameConfigProvider;
use Atsmacode\PokerGame\PokerGameRelConfigProviderFactory;
use Laminas\ServiceManager\ServiceManager;

$config                 = (new PokerGameConfigProvider('../'))->get();
$pokerGameDependencyMap = $config['dependencies'];
$serviceManager         = new ServiceManager($pokerGameDependencyMap);

$serviceManager->setFactory(PokerGameConfigProvider::class, new PokerGameRelConfigProviderFactory());
