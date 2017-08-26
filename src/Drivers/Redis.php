<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Redis driver.
 *
 * Accepted options:
 * host - redis server host
 * port - (optional) redis server port
 * password - (optional) redis server password
 */
class Redis implements IDriver
{
    /**
     * @var \Redis
     */
    private $redis;

    public function __construct($options)
    {
        $server = $options;

        if (!isset($server['host'])) {
            throw new DriverOptionsInvalidException('Must pass redis a host option!');
        }

        $this->redis = new \Redis();

        $connected = $this->redis->connect($server['host'], isset($server['port']) ? $server['port'] : null);

        if (!$connected) {
            throw new \Exception('Failed to connect to Redis: '.$this->redis->getLastError());
        }

        if (isset($server['password'])) {
            $authenticated = $this->redis->auth($server['password']);

            if (!$authenticated) {
                throw new \Exception('Failed to authenticate with Redis: '.$this->redis->getLastError());
            }
        }

        if (isset($server['database'])) {
            $success = $this->redis->select($server['database']);

            if (!$success) {
                throw new \Exception('Failed to set redis database '.$this->redis->getLastError());
            }
        }
    }

    public function set($key, $value, $time)
    {
        return $this->redis->set($key, $value, $time);
    }

    public function has($key)
    {
        return $this->redis->exists($key);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function remove($key)
    {
        // Every deletion in Redis is a successful deletion... I hope
        $this->redis->delete($key);

        return true;
    }

    public function clear()
    {
        return $this->redis->flushDB();
    }
}
