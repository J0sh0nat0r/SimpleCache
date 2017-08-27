<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\File as FileDriver;

/**
 * Tests the File driver.
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\File
 */
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

    public function testItemOverwriting()
    {
        $this->driver->set('foo', 'bar', 0);

        $this->assertEquals('bar', $this->driver->get('foo'));

        $this->assertTrue($this->driver->set('foo', 'baz', 10));

        $this->assertEquals('baz', $this->driver->get('foo'));
    }
}
