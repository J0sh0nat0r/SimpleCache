<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Drivers\SQLite as SQLiteDriver;

/**
 * Tests the SQLite driver.
 *
 * @covers \J0sh0nat0r\SimpleCache\Drivers\SQLite
 */
class SQLiteDriverTest extends DriverTestCase
{
    private $file;

    public function setUp(): void
    {
        $this->file = sys_get_temp_dir() . '/simple-cache-test-database.sqlite3';

        $this->driver = new SQLiteDriver([
            'file' => $this->file
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unlink($this->file);
    }

    public function testFileCreation()
    {
        $file = sys_get_temp_dir() . '/sc-' . sha1(rand(100, 200)) . '.sqlite3';

        new SQLiteDriver([
            'file' => $file
        ]);

        $this->assertTrue(file_exists($file));

        unlink($file);
    }
}
