<?php

namespace J0sh0nat0r\SimpleCache;

use J0sh0nat0r\SimpleCache\Drivers\NullDriver;
use PHPUnit\Framework\TestCase;

/**
 * Tests the null driver
 *
 * @covers NullDriver;
 */
class NullDriverTest extends TestCase {
    /**
     * @var Cache
     */
    private $cache;

    public function setUp() {
        $this->cache = new Cache(NullDriver::class);
    }

    public function tearDown() {
        $this->cache = null;
    }

    public function testSetFails()
    {
        $this->assertFalse($this->cache->store('testing', 'testing', 0));
    }

    public function testHasReturnsFalse()
    {
        $this->assertFalse($this->cache->has('testing'));
    }

    public function testGetReturnsNull()
    {
        $this->assertNull($this->cache->get('testing'));
    }

    public function testRemoveFails()
    {
        $this->assertFalse($this->cache->remove('testing'));
    }

    public function testClearFails()
    {
        $this->assertFalse($this->cache->clear());
    }
}