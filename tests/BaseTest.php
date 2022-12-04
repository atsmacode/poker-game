<?php

namespace Atsmacode\PokerGame\Tests;

use Atsmacode\PokerGame\PokerGameConfigProvider;
use Doctrine\DBAL\DriverManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['THE_ROOT'] = '';
        $GLOBALS['dev']      = true;
        $config              = (new PokerGameConfigProvider)->get();
        $env                 = 'test';

        $GLOBALS['connection'] = DriverManager::getConnection([
            'dbname'   => $config['db'][$env]['database'],
            'user'     => $config['db'][$env]['username'],
            'password' => $config['db'][$env]['password'],
            'host'     => $config['db'][$env]['servername'],
            'driver'   => $config['db'][$env]['driver'],
        ]);

        $pokerGameDependencyMap  = require('config/dependencies.php');

        $this->container = new ServiceManager($pokerGameDependencyMap);
    }
}
