<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;

/**
 * Tests the array driver.
 *
 * @covers ArrayDriver
 */
class ArrayDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new ArrayDriver();
    }
}
