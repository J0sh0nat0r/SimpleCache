<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Cache;
use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;
use PHPUnit\Framework\TestCase;

/**
 * Tests the main Cache class.
 *
 * @covers \J0sh0nat0r\SimpleCache\Cache
 */
class CacheTest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    public function setUp()
    {
        $this->cache = new Cache(ArrayDriver::class);

        $this->cache->remember_values = false;
    }

    public function testStore()
    {
        $this->assertTrue($this->cache->store('foo', 'bar'));
    }

    /**
     * @depends testStore
     */
    public function testGet()
    {
        $this->assertNull($this->cache->get('foo'));

        $this->assertTrue($this->cache->store('foo', 'bar'));

        $this->assertEquals('bar', $this->cache->get('foo'));

        $this->assertEquals('baz', $this->cache->get('qux', 'baz'));
    }

    /**
     * @depends testStore
     */
    public function testHas()
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->assertTrue($this->cache->store('foo', 'bar'));

        $this->assertTrue($this->cache->has('foo'));
    }

    /**
     * @depends testStore
     * @depends testGet
     * @depends testHas
     */
    public function testPull()
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->assertTrue($this->cache->store('foo', 'bar'));

        $this->assertEquals('foo', $this->cache->pull('foo'));

        $this->assertFalse($this->cache->has('foo'));

        $this->assertEquals('baz', $this->cache->pull('foo', 'baz'));
    }

    /**
     * @depends testStore
     * @depends testHas
     */
    public function testRemove()
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->assertTrue($this->cache->store('foo', 'bar'));

        $this->assertTrue($this->cache->has('foo'));

        $this->assertTrue($this->cache->remove('foo'));

        $this->assertFalse($this->cache->has('foo'));
    }

    /**
     * @depends testStore
     * @depends testHas
     */
    public function testForever()
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->assertTrue($this->cache->forever('foo', 'bar'));

        $this->assertTrue($this->cache->has('foo'));
    }

    /**
     * @depends testHas
     */
    public function testStoreArray()
    {
        $this->assertFalse($this->cache->has('foo'));
        $this->assertFalse($this->cache->has('baz'));

        $this->assertEquals(
            ['foo' => true, 'baz' => true],
            $this->cache->store(['foo' => 'bar', 'baz' => 'qux'])
        );

        $this->assertTrue($this->cache->has('foo'));
        $this->assertTrue($this->cache->has('baz'));
    }

    /**
     * @depends testStoreArray
     *
     */
    public function testHasArray()
    {
        $this->assertEquals(
            ['foo' => false, 'baz' => false],
            $this->cache->has(['foo', 'baz'])
        );

        $this->assertEquals(
            ['foo' => true, 'baz' => true],
            $this->cache->store(['foo' => 'bar', 'baz' => 'qux'])
        );

        $this->assertEquals(
            ['foo' => true, 'baz' => true, 'quux' => false],
            $this->cache->has(['foo', 'baz', 'quux'])
        );
    }

    /**
     * @depends testStoreArray
     * @depends testHasArray
     */
    public function testForeverArray()
    {
        $this->assertEquals(
            ['foo' => false, 'baz' => false],
            $this->cache->has(['foo', 'baz'])
        );

        $this->assertEquals(
            ['foo' => true, 'baz' => true],
            $this->cache->forever(['foo' => 'bar', 'baz' => 'qux'])
        );

        $this->assertEquals(
            ['foo' => true, 'baz' => true],
            $this->cache->has(['foo', 'baz'])
        );
    }
}
