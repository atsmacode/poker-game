<?php

use Atsmacode\PokerGame\PokerGameConfigProvider;
use Laminas\ServiceManager\ServiceManager;

$config                 = (new PokerGameConfigProvider())->get('../');
$pokerGameDependencyMap = $config['dependencies'];
$serviceManager         = new ServiceManager($pokerGameDependencyMap);
