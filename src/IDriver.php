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
    public function put(string $key, $value, int $time): bool;

    /**
     * Check if a value is stored in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get a value from the cache.
     *
     * @param string $key
     *
     * @return ?string
     */
    public function get(string $key): ?string;

    /**
     * Remove a value from the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove(string $key): bool;

    /**
     * Clear the cache.
     *
     * @return mixed
     */
    public function clear();
}
