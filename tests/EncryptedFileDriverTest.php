<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\File;

class EncryptedFileDriverTest extends FileDriverTest
{
    public function setUp()
    {
        $this->dir = sys_get_temp_dir().'/simple-cache-test-dir';

        $this->driver = new File([
            'dir' => $this->dir,
            'encryption_key' => sha1(openssl_random_pseudo_bytes(40))
        ]);
    }

    public function testOutputIsEncrypted()
    {
        $this->driver->set('foo', 'bar', 0);

        $output = file_get_contents($this->dir.'/'.sha1('foo').'/item.dat');

        $this->assertNotContains('foo', $output, 'The data was not encrypted and appeared in item.dat');
    }
}
