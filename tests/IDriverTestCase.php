<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests a driver
 *
 * @covers ArrayDriver;
 */
abstract class IDriverTestCase extends TestCase
{
    /**
     * @var \J0sh0nat0r\SimpleCache\IDriver
     */
    protected $driver;

    public function tearDown()
    {
        $this->driver->clear();

        $this->driver = null;
    }

    public function testSet()
    {
        $this->assertTrue($this->driver->set('foo', 'bar', 0));
    }

    /**
     * @depends testSet
     */
    public function testHas()
    {
        $this->assertFalse($this->driver->has('foo'));

        $this->driver->set('foo', 'bar', 0);

        $this->assertTrue($this->driver->has('foo'));
    }

    /**
     * @depends testSet
     */
    public function testGet()
    {
        $this->driver->set('foo', 'bar', 0);

        $this->assertEquals('bar', $this->driver->get('foo'));
        $this->assertNull($this->driver->get('baz'));
    }

    /**
     * @depends testSet, testHas
     */
    public function testRemove()
    {
        $this->driver->set('foo', 'bar', 0);

        $this->assertTrue($this->driver->remove('foo'));
        $this->assertFalse($this->driver->has('foo'));
    }

    /**
     * @depends testSet, testHas
     */
    public function testClear()
    {
        $this->driver->set('foo', 'bar', 0);
        $this->driver->set('baz', 'qux', 0);

        $this->driver->clear();

        $this->assertFalse($this->driver->has('foo'));
        $this->assertFalse($this->driver->has('baz'));
    }

    /**
     * @depends testSet, testHas
     */
    public function testItemExpiration()
    {
        $this->driver->set('foo', 'bar', 1);

        sleep(2);

        $this->assertFalse($this->driver->has('foo'));
    }
}