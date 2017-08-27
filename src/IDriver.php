<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache;

/**
 * The interface which provides the template for drivers.
 */
interface IDriver
{
    /**
     * Put a value in the cache.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $time
     *
     * @return bool
     */
    public function put($key, $value, $time);

    /**
     * Check if a value is stored in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Get a value from the cache.
     *
     * @param string $key
     *
     * @return string
     */
    public function get($key);

    /**
     * Remove a value from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);

    /**
     * Clear the cache.
     *
     * @return mixed
     */
    public function clear();
}
