<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Tests;

use Exception;
use J0sh0nat0r\SimpleCache\Cache as SimpleCache;
use J0sh0nat0r\SimpleCache\Drivers\ArrayDriver;
use J0sh0nat0r\SimpleCache\StaticFacade as Cache;
use PHPUnit\Framework\TestCase;

class StaticFacadeTest extends TestCase
{
    /**
     * @var SimpleCache
     */
    private SimpleCache $cache;

    public function setUp(): void
    {
        $this->cache = new SimpleCache(ArrayDriver::class);

        $this->cache->remember_values = false;
    }

    public function testNotBoundException()
    {
        $this->expectException(Exception::class);
        Cache::get('foo');
    }

    public function testFacade()
    {
        Cache::bind($this->cache);

        $this->cache->store('foo', 'bar');

        $this->assertEquals('bar', Cache::get('foo'));

        $this->assertEquals(null, Cache::get('baz'));

        $this->assertEquals(false, Cache::get('baz', false));
    }

    public function testInvalidMethodException()
    {
        $this->expectError();
        $this->expectErrorMessage("Call to undefined method J0sh0nat0r\SimpleCache\Cache::foo()");

        Cache::bind($this->cache);

        Cache::foo();
    }
}
