<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException;
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
        if (!isset($options['host'])) {
            throw new DriverOptionsInvalidException('The host option is required');
        }

        if (!is_string($options['host'])) {
            throw new DriverOptionsInvalidException('The host option must be a string');
        }

        $options['port'] = isset($options['port']) ? $options['port'] : 6379;

        if (!is_numeric($options['port'])) {
            throw new DriverOptionsInvalidException('The port option must be numeric');
        }

        $this->redis = new \Redis();

        $connected = $this->redis->connect($options['host'], intval($options['port']));

        if (!$connected) {
            throw new DriverInitializationFailedException('Failed to connect to Redis: '.$this->redis->getLastError());
        }

        if (isset($options['password'])) {
            if (!is_string($options['password'])) {
                throw new DriverOptionsInvalidException('The password option must be a string');
            }

            $authenticated = $this->redis->auth($options['password']);

            if (!$authenticated) {
                throw new DriverInitializationFailedException(
                    'Failed to authenticate with Redis: '.$this->redis->getLastError()
                );
            }
        }

        if (isset($options['database'])) {
            if (!is_numeric($options['database'])) {
                throw new DriverOptionsInvalidException('The database option must be numeric');
            }

            $success = $this->redis->select(intval($options['database']));

            if (!$success) {
                throw new DriverInitializationFailedException(
                    'Failed to select Redis database: '.$this->redis->getLastError()
                );
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
