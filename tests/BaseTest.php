<?php

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     *  php ./vendor/bin/phpunit tests --color
     */
    public function setUp(): void
    {
        parent::setUp();

        require 'vendor/autoload.php';
    }
}