<?php

namespace Atsmacode\PokerGame\Tests;

use Atsmacode\Framework\DatabaseProvider;
use Atsmacode\PokerGame\PokerGameConfigProvider;
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

        $GLOBALS['connection'] = DatabaseProvider::getConnection($config, $env);

        $pokerGameDependencyMap  = require('config/dependencies.php');

        $this->container = new ServiceManager($pokerGameDependencyMap);
    }
}
