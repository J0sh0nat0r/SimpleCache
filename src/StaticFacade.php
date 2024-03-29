<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache;

use Closure;
use Exception;

/**
 * A static wrapper around a SimpleCache instance (e.g for a global cache).
 *
 * @method static bool|bool[]  store(string | string[] $key, mixed $value, int $time = null)
 * @method static mixed        remember(string $key, int $time, Closure $generate, mixed $default = null)
 * @method static bool|bool[]  forever(string | string[] $key, mixed $value = null)
 * @method static bool|bool[]  has(string | string[] $key)
 * @method static mixed        get(string | string[] $key, mixed $default = null)
 * @method static mixed        pull(string | string[] $key, mixed $default = null)
 * @method static bool|bool[]  remove(string | string[] $key)
 * @method static bool         clear()
 */
class StaticFacade
{
    /**
     * Cache instance for the static facade.
     *
     * @var Cache
     */
    private static Cache $cache;

    /**
     * Handle static calls and proxy them to $cache.
     *
     * @param string     $name      Name of the function being called
     * @param array|null $arguments Arguments passed yo the function being called
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function __callStatic(string $name, ?array $arguments)
    {
        self::_checkBound();

        $arguments ??= [];

        return self::$cache->{$name}(...$arguments);
    }

    /**
     * Bind the StaticFacade to a SimpleCache instance.
     *
     * @param Cache $cache The SimpleCache instance to bind to
     */
    public static function bind(Cache $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * Checks if the StaticFacade has been bound to a SimpleCache instance,
     * and, if not, an exception will be thrown.
     *
     * @throws Exception
     *
     * @return void
     */
    private static function _checkBound(): void
    {
        if (!isset(self::$cache)) {
            throw new Exception(
                'Please bind StaticFacade to a SimpleCache instance with the `bind` method'
            );
        }
    }
}
