<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\APC as APCDriver;

class APCDriverTest extends DriverTestCase
{
    public static function setUpBeforeClass()
    {
        ini_set('apc.enable_cli', true);
    }
    
    public function setUp()
    {
        $this->driver = new APCDriver();
    }
}
