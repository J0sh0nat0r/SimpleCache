<?php

namespace J0sh0nat0r\SimpleCache;

/**
 * The interface which provides the template for drivers
 *
 * Interface IDriver
 * @package J0sh0nat0r\SimpleCache
 */
interface IDriver
{
    /**
     * Store a value in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $time
     * @return bool
     */
    function set($key, $value, $time);

    /**
     * Get a value from the cache
     *
     * @param string $key
     * @return mixed
     */
    function get($key);

    /**
     * Remove a value from the cache
     *
     * @param string $key
     * @return bool
     */
    function remove($key);

    /**
     * Clear the cache
     *
     * @return mixed
     */
    function clear();
}