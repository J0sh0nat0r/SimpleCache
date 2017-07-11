<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache;

/**
 * The interface which provides the template for drivers
 *
 * Interface IDriver
 *
 * @package J0sh0nat0r\SimpleCache
 */
interface IDriver
{
    /**
     * Store a value in the cache
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $time
     * @return bool
     */
    function set($key, $value, $time);

    /**
     * Check if a value is stored in the cahce
     *
     * @param  string $key
     * @return bool
     */
    function has($key);

    /**
     * Get a value from the cache
     *
     * @param  string $key
     * @return mixed
     */
    function get($key);

    /**
     * Remove a value from the cache
     *
     * @param  string $key
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
