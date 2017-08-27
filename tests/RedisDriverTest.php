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
 * @covers \J0sh0nat0r\SimpleCache\Drivers\Redis
 */
class RedisDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new RedisDriver([
            'host' => 'localhost',
        ]);
    }

    /**
     * @expectedException DriverOptionsInvalidException
     */
    public function testHostOptionIsRequired()
    {
        new RedisDriver([]);
    }

    /**
     * @expectedException DriverOptionsInvalidException
     */
    public function testHostOptionMustBeString()
    {
        new RedisDriver([
            'host' => 011001100110111101101111
        ]);
    }

    /**
     * @expectedException DriverOptionsInvalidException
     */
    public function testPortOptionMustBeNumeric()
    {
        new RedisDriver([
            'host' => 'localhost',
            'port' => 'foo'
        ]);
    }

    /**
     * @expectedException DriverOptionsInvalidException
     */
    public function testPasswordOptionMustBeString()
    {
        new RedisDriver([
            'host' => 'localhost',
            'password' => ['foo', 'bar']
        ]);
    }

    /**
     * @expectedException DriverOptionsInvalidException
     */
    public function testDatabaseOptionMustBeNumeric()
    {
        new RedisDriver([
            'host' => 'localhost',
            'database' => 'foo'
        ]);
    }

    /**
     * @expectedException DriverInitializationFailedException
     */
    public function testConnectionErrorException()
    {
        new RedisDriver([
            'host' => '0.0.0.0'
        ]);
    }
}
