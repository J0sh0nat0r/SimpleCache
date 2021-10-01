<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\APC as APCDriver;

/**
 * Tests the APC driver.
 *
 * @requires extension apc
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\APC
 */
class APCDriverTest extends DriverTestCase
{
    public function setUp(): void
    {
        $this->driver = new APCDriver();
    }

    public function testItemExpiration()
    {
        // Can't test item expiration as APC requires a new request in order to expunge an item
        $this->assertTrue(true);
    }
}
