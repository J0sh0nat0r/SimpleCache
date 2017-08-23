<?php

namespace J0sh0nat0r\SimpleCache;

use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;
use PHPUnit\Framework\TestCase;

/**
 * Tests the array driver
 *
 * @covers ArrayDriver;
 */
class ArrayDriverTest extends TestCase
{
    /**
     * @var  ArrayDriver $driver
     */
    private $driver;

    public function setUp()
    {
        $this->driver = new ArrayDriver;
    }

    public function tearDown()
    {
        $this->driver = null;
    }

    public function testSetSucceeds()
    {
        $this->assertTrue($this->driver->set('testing', 'testing', 0));
    }

    public function testHasReturnsFalse()
    {
        $this->assertFalse($this->driver->has('testing'));
    }

    public function testGetReturnsNull()
    {
        $this->assertNull($this->driver->get('testing'));
    }

    public function testRemoveSucceeds()
    {
        $this->assertTrue($this->driver->remove('testing'));
    }

    public function testClearSucceeds()
    {
        $this->assertTrue($this->driver->clear());
    }
}