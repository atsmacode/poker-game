<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['THE_ROOT'] = '';
        $GLOBALS['dev'] = true;
    }
}
