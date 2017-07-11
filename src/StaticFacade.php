<?php

namespace J0sh0nat0r\SimpleCache;

/**
 * A static wrapper around a SimpleCache instance (e.g for a global cache)
 *
 * @category Class
 * @author   Josh P
 * @package  J0sh0nat0r\SimpleCache
 */
class StaticFacade
{
    /**
     * Cache instance for the static facade
     *
     * @var Cache
     */
    private static $cache;

    /**
     * Bind the StaticFacade to a SimpleCache instance
     *
     * @param mixed $cache
     */
    public static function setCache($cache)
    {
        self::$cache = $cache;
    }

    /**
     * Store an item in the cache indefinitely
     *
     * @param $key
     * @param null $value
     *
     * @return bool
     */
    public static function forever($key, $value = null)
    {
        self::_checkBound();

        return self::$cache->forever($key, $value);
    }

    /**
     * Store a value (or an array of key-value pairs) in the cache
     *
     * @param $key
     * @param null $value
     * @param int  $time
     * 
     * @return bool|array
     * @throws \Exception
     */
    public static function store($key, $value = null, $time = null)
    {
        self::_checkBound();

        return self::$cache->store($key, $value, $time);
    }

    /**
     * Try to find a value in the cache and return it,
     * if we can't it will be calculated with the provided closure
     *
     * @param string   $key      Key of the item to remember
     * @param int      $time     Time to remember the item for (seconds)
     * @param \Closure $generate Function used to generate the value to remember
     * @param mixed    $default  Default return value (if item not found & generate returns null)
     *
     * @return mixed
     */
    public static function remember($key, $time, $generate, $default = null)
    {
        self::_checkBound();

        return self::$cache->remember($key, $time, $generate, $default);
    }

    /**
     * Fetch a value (or an multiple values) from the cache
     *
     * @param string $key     Key(s) of the items to fetch
     * @param null   $default Default return value (if item not found)
     *
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        self::_checkBound();

        return self::$cache->get($key, $default);
    }

    /**
     * Check if the cache contains an item
     *
     * @param string|array $key The key (or keys) to search for
     *
     * @return bool|array
     */
    public static function has($key)
    {
        self::_checkBound();

        return self::$cache->has($key);
    }

    /**
     * Fetch a value (or multiple values), remove it from the cache and then return it
     *
     * @param string|array $key     Key(s) of the item(s) to pull
     * @param null         $default Default return value (if item not found)
     *
     * @return mixed
     */
    public static function pull($key, $default = null)
    {
        self::_checkBound();

        return self::$cache->pull($key, $default);
    }

    /**
     * Remove a value (or multiple values) from the cache
     *
     * @param string|array $key Key(s) of the item(s) to remove
     *
     * @return bool|array
     */
    public static function remove($key)
    {
        self::_checkBound();

        return self::$cache->remove($key);
    }


    /**
     * Checks if the StaticFacade has been bound to a SimpleCache instance,
     * and, if not, an exception will be thrown.
     *
     * @throws \Exception
     * @return void
     */
    private static function _checkBound()
    {
        if (!isset(self::$cache)) {
            throw new \Exception(
                'Please bind StaticFacade to a SimpleCache instance with setCache'
            );
        }
    }
}
