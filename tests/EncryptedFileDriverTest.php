<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\File as FileDriver;

/**
 * Tests teh file driver with encryption enabled.
 */
class EncryptedFileDriverTest extends FileDriverTest
{
    public function setUp()
    {
        $this->dir = sys_get_temp_dir().'/simple-cache-test-dir';

        $this->driver = new FileDriver([
            'dir' => $this->dir,
            'encryption_key' => 'Unit Testing'
        ]);
    }

    public function testOutputIsEncrypted()
    {
        $this->driver->put('foo', 'bar', 0);

        $output = file_get_contents($this->dir.'/'.sha1('foo').'/item.dat');

        $this->assertNotContains('foo', $output, 'The data was not encrypted and appeared in item.dat');
    }
}
