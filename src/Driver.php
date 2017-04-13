<?php

namespace J0sh0nat0r\SimpleCache;

/**
 * The base driver class from which all drivers should inherit
 *
 * Class Driver
 * @package J0sh0nat0r\SimpleCache
 */
abstract class Driver
{
    /**
     * Store a value in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $time
     * @return bool
     */
    abstract public function set($key, $value, $time);

    /**
     * Get a value from the cache
     *
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * Remove a value from the cache
     *
     * @param string $key
     * @return bool
     */
    abstract public function remove($key);

    /**
     * Clear the cache
     *
     * @return mixed
     */
    abstract public function clear();
}