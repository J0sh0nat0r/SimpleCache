<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\Redis as RedisDriver;

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
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException
     */
    public function testHostOptionIsRequired()
    {
        new RedisDriver([]);
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException
     */
    public function testHostOptionMustBeString()
    {
        new RedisDriver([
            'host' => 011001100110111101101111
        ]);
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException
     */
    public function testPortOptionMustBeNumeric()
    {
        new RedisDriver([
            'host' => 'localhost',
            'port' => 'foo'
        ]);
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException
     */
    public function testPasswordOptionMustBeString()
    {
        new RedisDriver([
            'host' => 'localhost',
            'password' => ['foo', 'bar']
        ]);
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException
     */
    public function testDatabaseOptionMustBeNumeric()
    {
        new RedisDriver([
            'host' => 'localhost',
            'database' => 'foo'
        ]);
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException
     */
    public function testConnectionErrorException()
    {
        new RedisDriver([
            'host' => '0.0.0.0'
        ]);
    }
}
