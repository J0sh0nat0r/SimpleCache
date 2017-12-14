<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException;
use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Memcached driver.
 *
 *
 * Accepted options:
 *   * servers: Array of servers
 */
class Memcached implements IDriver
{
    /**
     * Pool of Memcache servers (instance of \Memcached).
     *
     * @var \Memcached
     */
    private $pool;

    /**
     * Memcached constructor.
     *
     * @param $options
     *
     * @throws DriverInitializationFailedException
     * @throws DriverOptionsInvalidException
     */
    public function __construct($options)
    {
        $this->pool = new \Memcached();

        if (!isset($options['servers']) || !is_array($options['servers'])) {
            if (!isset($options['host'])) {
                throw new DriverOptionsInvalidException('Please provide at least 1 server to the driver');
            }

            $options = [
                'servers' => [$options],
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

            $success = $this->pool->addServer($server['host'], $server['port'], $server['weight']);

            if (!$success) {
                throw new DriverInitializationFailedException(
                    'Failed to add a Memcached server: '.$this->pool->getResultMessage()
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function put($key, $value, $time)
    {
        $expiration = $time > 0 ? time() + $time : 0;

        return $this->pool->set($key, $value, $expiration);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $keys = $this->pool->getAllKeys();

        if (!is_array($keys)) {
            return false;
        }

        return array_search($key, $keys) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $result = $this->pool->get($key);

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->pool->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->pool->flush();
    }
}
