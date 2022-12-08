<?php

namespace Atsmacode\PokerGame\Tests;

use Atsmacode\Framework\Database\ConnectionInterface;
use Atsmacode\PokerGame\Database\DbalTestFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $pokerGameDependencyMap  = require('config/dependencies.php');

        $this->container = new ServiceManager($pokerGameDependencyMap);
        $this->container->setFactory(ConnectionInterface::class, new DbalTestFactory());
    }
}
