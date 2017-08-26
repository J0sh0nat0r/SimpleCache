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
 * servers: Array of servers
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

        if (!isset($options['servers']) || !is_array($options['servers'])) {
            if (!isset($options['host'])) {
                throw new DriverOptionsInvalidException('Please provide at least 1 server to the driver');
            }

            $options = [
                'servers' => [$options]
            ];
        }

        foreach ($options['servers'] as $server) {
            if (!is_array($server)) {
                throw new DriverOptionsInvalidException('Each server must be an array');
            }

            if (!isset($server['host'])) {
                throw new DriverOptionsInvalidException('The host option is required for each server');
            }

            $server['port'] = isset($server['port']) ? $server['port'] : 11211;

            if (!is_numeric($server['port'])) {
                throw new DriverOptionsInvalidException('Server port option must be numeric');
            }

            $server['weight'] = isset($server['weight']) ? $server['weight'] : 0;

            if (!is_numeric($server['weight'])) {
                throw new DriverOptionsInvalidException('Server weight option must be numeric');
            }

            $this->pool->addServer($server['host'], $server['port'], $server['weight']);
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
