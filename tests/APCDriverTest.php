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
        $this->iniSet('apc.enable', 1);
        $this->iniSet('apc.enable_cli', 1);

        $this->driver = new APCDriver();
    }
}
