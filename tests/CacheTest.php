<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Cache;
use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;

/**
 * Tests the main Cache class.
 *
 * @covers \J0sh0nat0r\SimpleCache\Cache
 */
class CacheTest extends \PHPUnit_Framework_TestCase
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
    public function testRemember()
    {
        function cachedTime(Cache $cache)
        {
            return $cache->remember('foo', 2, function () {
                return time();
            });
        }

        $time = cachedTime($this->cache);

        $this->assertEquals($time, cachedTime($this->cache));

        sleep(1);

        $this->assertEquals($time, cachedTime($this->cache));

        sleep(1);

        $this->assertEquals(time(), cachedTime($this->cache));

        $this->assertEquals('baz', $this->cache->remember('qux', 0, function () {
            return null;
        }, 'baz'));

        $this->assertFalse($this->cache->has('baz'));
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
     * @depends testGet
     * @depends testHas
     * @depends testRemove
     */
    public function testPull()
    {
        $this->assertFalse($this->cache->has('foo'));

        $this->assertTrue($this->cache->store('foo', 'bar'));

        $this->assertEquals('bar', $this->cache->pull('foo'));

        $this->assertFalse($this->cache->has('foo'));

        $this->assertEquals('baz', $this->cache->pull('foo', 'baz'));
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
    public function testRemoveArray()
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
            ['foo' => true, 'baz' => true],
            $this->cache->remove(['foo', 'baz'])
        );

        $this->assertEquals(
            ['foo' => false, 'baz' => false],
            $this->cache->has(['foo', 'baz'])
        );
    }

    /**
     * @depends testStoreArray
     * @depends testHasArray
     * @depends testRemoveArray
     */
    public function testPullArray()
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
            ['foo' => 'bar', 'baz' => 'qux'],
            $this->cache->pull(['foo', 'baz'])
        );

        $this->assertEquals(
            ['foo' => false, 'baz' => false],
            $this->cache->has(['foo', 'baz'])
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

    /**
     * @depends testStoreArray
     * @depends testHasArray
     */
    public function testClear()
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
            ['foo' => true, 'baz' => true],
            $this->cache->has(['foo', 'baz'])
        );

        $this->assertTrue($this->cache->clear());

        $this->assertEquals(
            ['foo' => false, 'baz' => false],
            $this->cache->has(['foo', 'baz'])
        );
    }

    /**
     * @expectedException \J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException
     */
    public function testInvalidKeyExceptionIsThrown()
    {
        $this->cache->store(null, 'foo');
    }
}
