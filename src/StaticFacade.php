<?php

namespace J0sh0nat0r\SimpleCache;

/**
 * A static wrapper around a SimpleCache instance (e.g for a global cache)
 *
 * Class StaticFacade
 * @package J0sh0nat0r\SimpleCache
 */
class StaticFacade {
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

    public static function store($key, $value = null, $time = null)
    {
        self::checkBound();

        return self::$cache->store($key, $value, $time);
    }

    public static function forever($key, $value = null)
    {
        self::checkBound();

        return self::$cache->forever($key, $value);
    }

    public static function get($key, $default = null)
    {
        self::checkBound();

        return self::$cache->get($key, $default);
    }

    public static function remember($key, $time, $generate, $default = null)
    {
        self::checkBound();

        return self::$cache->remember($key, $time, $generate, $default);
    }

    public static function has($key)
    {
        self::checkBound();

        return self::$cache->has($key);
    }

    public static function pull($key, $default = null)
    {
        self::checkBound();

        return self::$cache->pull($key, $default);
    }

    public static function remove($key)
    {
        self::checkBound();

        return self::$cache->remove($key);
    }


    /**
     * Checks if the StaticFacade has been bound to a SimpleCache instance,
     * and, if not, an exception will be thrown.
     *
     * @throws \Exception
     */
    private static function checkBound()
    {
        if(!isset(self::$cache))
        {
            throw new \Exception('Please bind StaticFacade to a SimpleCache instance with setCache');
        }
    }
}