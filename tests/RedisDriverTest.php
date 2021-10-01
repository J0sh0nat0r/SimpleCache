<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\Redis as RedisDriver;
use J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException;
use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;

/**
 * Tests the Redis driver.
 *
 * @requires extension redis
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\Redis
 */
class RedisDriverTest extends DriverTestCase
{
    public function setUp(): void
    {
        $this->driver = new RedisDriver([
            'host' => 'localhost',
        ]);
    }

    public function testHostOptionIsRequired()
    {
        $this->expectException(DriverOptionsInvalidException::class);
        new RedisDriver([]);
    }

    public function testHostOptionMustBeString()
    {
        $this->expectException(DriverOptionsInvalidException::class);
        new RedisDriver([
            'host' => 011001100110111101101111
        ]);
    }

    public function testPortOptionMustBeNumeric()
    {
        $this->expectException(DriverOptionsInvalidException::class);
        new RedisDriver([
            'host' => 'localhost',
            'port' => 'foo'
        ]);
    }

    public function testPasswordOptionMustBeString()
    {
        $this->expectException(DriverOptionsInvalidException::class);
        new RedisDriver([
            'host' => 'localhost',
            'password' => ['foo', 'bar']
        ]);
    }

    public function testDatabaseOptionMustBeNumeric()
    {
        $this->expectException(DriverOptionsInvalidException::class);
        new RedisDriver([
            'host' => 'localhost',
            'database' => 'foo'
        ]);
    }

    public function testConnectionErrorException()
    {
        $this->expectException(DriverInitializationFailedException::class);
        new RedisDriver([
            'host' => 'foo.bar.baz.qux'
        ]);
    }

    public function testDatabaseSelectionErrorException()
    {
        $this->expectException(DriverInitializationFailedException::class);
        new RedisDriver([
            'host' => 'localhost',
            'database' => PHP_INT_MAX
        ]);
    }
}
