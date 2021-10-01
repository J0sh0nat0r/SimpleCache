<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;

/**
 * Tests the array driver.
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\ArrayDriver
 */
class ArrayDriverTest extends DriverTestCase
{
    public function setUp(): void
    {
        $this->driver = new ArrayDriver();
    }
}
