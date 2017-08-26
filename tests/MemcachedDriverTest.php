<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\Memcached as MemcachedDriver;

/**
 * Tests the Memcached driver.
 *
 * @covers MemcachedDriver
 */
class MemcachedDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new MemcachedDriver([
            'host' => 'localhost'
        ]);
    }
}
