<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache;

use J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException;
use J0sh0nat0r\SimpleCache\Internal\PCI;

/**
 * The master cache class of SimpleCache.
 */
class Cache
{
    /**
     * The default storage time for cache items.
     *
     * @var int
     */
    public static $DEFAULT_TIME = 3600;

    /**
     * If set to true values that are fetched / stored during the
     * request will be remembered for the duration of the request.
     *
     * @var bool
     */
    public $remember_values = true;

    /**
     * Provides a Property based Cache Interface (or PCI) an
     * alternative (albeit more limited) way to access cache items.
     *
     * @var PCI
     */
    public $items;

    /**
     * The driver instance.
     *
     * @var IDriver
     */
    private $driver;

    /**
     * Array containing items that have previously been loaded.
     *
     * @var array
     */
    private $loaded = [];

    /**
     * Cache constructor.
     *
     * @param string     $driver         The driver to use
     * @param null|array $driver_options Options to pass to the driver
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($driver, $driver_options = null)
    {
        $this->items = new PCI($this);

        if (is_null($driver_options)) {
            $this->driver = new $driver();

            return;
        }

        if (!is_array($driver_options)) {
            throw new \InvalidArgumentException('The `driver_options` argument must be either `null` or an `array`');
        }

        $this->driver = new $driver($driver_options);
    }

    /**
     * Store a value (or an array of key-value pairs) in the cache.
     *
     * @param string|array $key   The key to store the item under(can also be a `key => value` array)
     * @param mixed        $value Value of the item (can also be the time in the case that $key is an array)
     * @param int          $time  Time to store the item for (can also be null in the case that $key is an array)
     *
     * @throws \InvalidArgumentException
     * @throws InvalidKeyException
     *
     * @return bool|bool[]
     */
    public function store($key, $value = null, $time = null)
    {
        $this->validateKey($key);

        if (!is_int($time) && !is_null($time)) {
            throw new \InvalidArgumentException('`time` must be an integer or null');
        }

        $time = is_null($time) ? self::$DEFAULT_TIME : max(0, $time);

        if (is_array($key)) {
            $time = is_null($value) ? $time : $value;
            $values = $key;

            $successes = [];
            foreach ($values as $key => $value) {
                $successes[$key] = $this->store($key, $value, $time);
            }

            return $successes;
        }

        if (!is_numeric($time)) {
            throw new \InvalidArgumentException('Time must be numeric');
        }

        $success = $this->driver->put($key, serialize($value), intval($time));

        if ($success && $this->remember_values) {
            $this->loaded[$key] = $value;
        }

        return $success;
    }

    /**
     * Store a item (or an array of items) in the cache indefinitely.
     *
     * @param string|array $key   The key to store the item under (can also be a `key => value` array)
     * @param mixed        $value Value of the item (leave null if $key is a `key => value` array)
     *
     * @throws InvalidKeyException
     * @throws \InvalidArgumentException
     *
     * @return bool|bool[]
     */
    public function forever($key, $value = null)
    {
        if (is_array($key)) {
            return $this->store($key, 0);
        }

        return $this->store($key, $value, 0);
    }

    /**
     * Try to find a value in the cache and return it,
     * if we can't it will be calculated with the provided closure.
     *
     * @param string   $key      Key of the item to remember
     * @param int      $time     Time to remember the item for
     * @param \Closure $generate Function used to generate the value
     * @param mixed    $default  Default value in case an item isn't found and $generate returns null (can be a callback)
     *
     * @throws InvalidKeyException
     *
     * @return mixed
     */
    public function remember($key, $time, $generate, $default = null)
    {
        $this->validateKey($key);

        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = call_user_func($generate, $key);

        if (!is_null($value)) {
            $this->store($key, $value, $time);

            return $value;
        }

        if (is_callable($default) && !is_string($default)) {
            return call_user_func($default, $key);
        }

        return $default;
    }

    /**
     * Check if the cache contains an item.
     *
     * @param string|string[] $key The key (or keys) to search for
     *
     * @throws InvalidKeyException
     *
     * @return bool|bool[]
     */
    public function has($key)
    {
        $this->validateKey($key);

        if (is_array($key)) {
            $keys = $key;

            $has = [];
            foreach ($keys as $key) {
                $has[$key] = $this->has($key);
            }

            return $has;
        }

        if ($this->remember_values && isset($this->loaded[$key])) {
            return true;
        }

        return $this->driver->has($key);
    }

    /**
     * Fetch a value (or an multiple values) from the cache.
     *
     * @param string|string[] $key     The key (or keys) to retrieve the values of
     * @param mixed           $default Default value in case an item isn't found (can be a callback)
     *
     * @throws InvalidKeyException
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->validateKey($key);

        if (is_array($key)) {
            $keys = $key;

            $results = [];
            foreach ($keys as $key) {
                $results[$key] = $this->get($key, $default);
            }

            return $results;
        }

        if ($this->remember_values && isset($this->loaded[$key])) {
            return $this->loaded[$key];
        }

        $result = $this->driver->get($key);

        if (is_null($result)) {
            if (is_callable($default) && !is_string($default)) {
                return call_user_func($default, $key);
            }

            return $default;
        }

        $result = unserialize($result);

        if ($this->remember_values) {
            $this->loaded[$key] = $result;
        }

        return $result;
    }

    /**
     * Fetch an item (or multiple items) from the cache, then remove it.
     *
     * @param string|string[] $key     Key of the item to pull (can also be an array of keys)
     * @param mixed           $default Default value in case an item isn't found (can be a callback)
     *
     * @throws InvalidKeyException
     *
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        $this->validateKey($key);

        $result = $this->get($key, $default);

        $this->remove($key);

        return $result;
    }

    /**
     * Remove an item (or multiple items) from the cache.
     *
     * @param string|string[] $key Key of the item to remove (can also be an array of keys)
     *
     * @throws InvalidKeyException
     *
     * @return bool|bool[]
     */
    public function remove($key)
    {
        $this->validateKey($key);

        if (is_array($key)) {
            $keys = $key;

            $successes = [];
            foreach ($keys as $key) {
                $successes[$key] = $this->remove($key);
            }

            return $successes;
        }

        if (isset($this->loaded[$key])) {
            unset($this->loaded[$key]);
        }

        return $this->driver->remove($key);
    }

    /**
     * Clears the cache, removing ALL items.
     *
     * @return bool
     */
    public function clear()
    {
        $this->loaded = [];

        return $this->driver->clear();
    }

    /**
     * Checks if a key is valid, and if not and exception will be thrown.
     *
     * @param mixed $key Key to validate
     *
     * @throws InvalidKeyException
     *
     * @return void
     */
    private function validateKey($key)
    {
        if (!is_array($key) && !is_string($key)) {
            throw new InvalidKeyException('The provided key was invalid');
        }
    }
}
