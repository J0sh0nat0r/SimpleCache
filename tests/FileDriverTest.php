<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\File as FileDriver;

class FileDriverTest extends DriverTestCase
{
    protected $dir;

    public function setUp()
    {
        $this->dir = sys_get_temp_dir().'/simple-cache-test-dir';

        $this->driver = new FileDriver([
            'dir' => $this->dir
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        rmdir($this->dir);
    }
}
