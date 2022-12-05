<?php

use Atsmacode\PokerGame\DbalLiveFactory;
use Doctrine\DBAL\Connection;

$GLOBALS['THE_ROOT'] = '../';

require('../vendor/autoload.php');
require('../config/container.php');

var_dump($serviceManager->get(Connection::class));
