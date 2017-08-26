<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\Redis as RedisDriver;

/**
 * Tests the Redis driver.
 *
 * @covers RedisDriver
 */
class RedisDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new RedisDriver([
            'host' => 'localhost',
        ]);
    }
}
