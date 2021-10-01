<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use J0sh0nat0r\SimpleCache\Cache;
use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;
use PHPUnit\Framework\TestCase;

/**
 * Tests the PCI.
 *
 * @covers \J0sh0nat0r\SimpleCache\Internal\PCI
 */
class PCITest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    public function setUp(): void
    {
        $this->cache = new Cache(ArrayDriver::class);

        $this->cache->remember_values = false;
    }

    public function testIsset()
    {
        $this->cache->items->foo = 'bar';

        $this->assertTrue(isset($this->cache->items->foo));
    }

    /**
     * @depends testIsset
     */
    public function testRetrieval()
    {
        $this->assertNull($this->cache->items->foo);

        $this->cache->items->foo = 'bar';

        $this->assertEquals('bar', $this->cache->items->foo);
    }

    /**
     * @depends testIsset
     */
    public function testRemoval()
    {
        $this->cache->items->foo = 'bar';

        unset($this->cache->items->foo);

        $this->assertFalse(isset($this->cache->items->foo));
    }
}
