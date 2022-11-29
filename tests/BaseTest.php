<?php

namespace Atsmacode\PokerGame\Tests;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['THE_ROOT']     = '';
        $GLOBALS['dev']          = true;
        $pokerGameDependencyMap  = require('config/dependencies.php');

        $this->container = new ServiceManager($pokerGameDependencyMap);
    }
}
