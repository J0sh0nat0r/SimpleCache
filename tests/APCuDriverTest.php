<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\APC as APCDriver;

/**
 * Tests the APCu driver.
 *
 * @requires extension apcu
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\APC
 */
class APCuDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new APCDriver();
    }

    public function testItemExpiration()
    {
        // Can't test item expiration as APCu requires a new request in order to expunge an item
    }
}
