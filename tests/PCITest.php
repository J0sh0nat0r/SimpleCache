<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Cache;
use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;

class PCITest extends \PHPUnit_Framework_TestCase
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

    public function testStorage()
    {
        $this->cache->items->foo = 'bar';
    }

    /**
     * @depends testStorage
     */
    public function testIsset()
    {
        $this->assertFalse(isset($this->cache->items->foo));

        $this->cache->items->foo = 'bar';

        $this->assertTrue(isset($this->cache->items->foo));
    }

    /**
     * @depends testStorage
     * @depends testIsset
     */
    public function testRetrieval()
    {
        $this->assertNull($this->cache->items->foo);

        $this->cache->items->foo = 'bar';

        $this->assertEquals('bar', $this->cache->items->foo);
    }

    /**
     * @depends testStorage
     * @depends testIsset
     */
    public function testRemoval()
    {
        $this->cache->items->foo = 'bar';

        $this->assertTrue(isset($this->cache->items->foo));

        unset($this->cache->items->foo);

        $this->assertFalse(isset($this->cache->items->foo));
    }
}
