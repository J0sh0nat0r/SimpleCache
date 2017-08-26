<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Memcached driver.
 *
 *
 * Accepted options:
 * servers - array of servers
 */
class Memcached implements IDriver
{
    /**
     * Pool of Memcache servers (instance of \Memcached).
     *
     * @var \Memcached
     */
    private $pool;

    public function __construct($options)
    {
        $this->pool = new \Memcached();

        if (!isset($options['servers'])) {
            throw new DriverOptionsInvalidException('Please provide at least 1 server to the SimpleCache memcached driver');
        }

        foreach ($options['servers'] as $server) {
            if (!(isset($server['host']) || !isset($server['port']))) {
                throw new DriverOptionsInvalidException('Missing host or port for SimpleCache Memcached server');
            }

            $this->pool->addServer($server['host'], $server['port']);
        }
    }

    public function set($key, $value, $time)
    {
        return $this->pool->set($key, $value, $time);
    }

    public function has($key)
    {
        return array_search($key, $this->pool->getAllKeys()) !== false;
    }

    public function get($key)
    {
        return $this->pool->get($key);
    }

    public function remove($key)
    {
        return $this->pool->delete($key);
    }

    public function clear()
    {
        return $this->pool->flush();
    }
}
