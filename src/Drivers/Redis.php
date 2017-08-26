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
 * host:     (required) Redis server host
 * port:     (optional) Redis server port
 * password: (optional) Redis server password
 */
class Redis implements IDriver
{
    /**
     * Redis server connection.
     *
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

        $connected = $this->redis->connect($server['host'], isset($server['port']) ? $server['port'] : 6379);

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
        if ($time === 0) {
            return $this->redis->set($key, $value);
        }

        return $this->redis->setex($key, $time, $value);
    }

    public function has($key)
    {
        return $this->redis->exists($key);
    }

    public function get($key)
    {
        $value = $this->redis->get($key);

        if ($value === false) {
            return null;
        }

        return $value;
    }

    public function remove($key)
    {
        return $this->redis->del($key) === 1;
    }

    public function clear()
    {
        return $this->redis->flushDB();
    }
}
