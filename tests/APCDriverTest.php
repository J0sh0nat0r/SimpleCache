<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\APC as APCDriver;

class APCDriverTest extends DriverTestCase
{
    public function setUp()
    {
        $this->driver = new APCDriver();
    }
}
