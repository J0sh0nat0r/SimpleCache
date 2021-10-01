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

    public function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/simple-cache-test-dir';

        $this->driver = new FileDriver([
            'dir' => $this->dir
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        rmdir($this->dir);
    }

    public function testDirectoryCreation()
    {
        $dir = sys_get_temp_dir() . '/sc-' . sha1(rand(0, 1000));

        $this->assertFalse(is_dir($dir));

        new FileDriver([
            'dir' => $dir
        ]);

        $this->assertTrue(is_dir($dir));

        rmdir($dir);
    }
}
