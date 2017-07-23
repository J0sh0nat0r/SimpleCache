<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache;

use J0sh0nat0r\SimpleCache\Internal\PCI;

/**
 * The master cache class of SimpleCache
 *
 * @package J0sh0nat0r\SimpleCache
 */
class Cache
{
    /**
     *  Default storage time for cache items
     */
    public static $DEFAULT_TIME = 3600;


    /**
     * @var  PCI  Provides a property based cache interface
     */
    public $items;


    /**
     * @var  IDriver
     */
    private $driver;
    /**
     * @var  array
     */
    private $loaded = [];


    /**
     * Cache constructor.
     *
     * @param string     $driver          The driver to use
     * @param null|array $driver_options  Options to pass to the driver
     */
    public function __construct($driver, $driver_options = null)
    {
        if (!is_null($driver_options)) {
            $this->driver = new $driver($driver_options);
        } else {
            $this->driver = new $driver();
        }

        $this->items = new PCI($this);
    }


    /**
     * Store a value (or an array of key-value pairs) in the cache
     *
     * @param  string|array $key    The key to store the item under(can also be a `key => value` array)
     * @param  mixed        $value  Value of the item (can also be the time in teh case that $key is an array)
     * @param  int          $time   Time to store the item for (can also be null in the case that $key is an array)
     *
     * @return bool|array
     *
     * @throws \Exception
     */
    public function store($key, $value = null, $time = null)
    {
        $time = empty($time) ? self::$DEFAULT_TIME : $time;

        if (is_array($key)) {
            $time = is_null($value) ? $time : $value;
            $values = $key;

            $success = [];
            foreach ($values as $key => $value) {
                $success[$key] = $this->store($key, $value, $time);
            }

            return $success;
        }

        if (!is_int($time)) {
            throw new \Exception('Time must be a number');
        }

        $success = $this->driver->set($key, serialize($value), $time);

        if ($success) {
            $this->loaded[$key] = $value;
        }

        return $success;
    }

    /**
     * Try to find a value in the cache and return it,
     * if we can't it will be calculated with the provided closure
     *
     * @param  string   $key       Key of the item to remember
     * @param  int      $time      Time to remember the item for
     * @param  \Closure $generate  Function used to generate the value
     * @param  mixed    $default   Default value in case an item isn't found and $generate returns null (can be a callback)
     *
     * @return mixed
     */
    public function remember($key, $time, $generate, $default = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = call_user_func($generate, $key);

        if (!is_null($value)) {
            $this->store($key, $value, $time);
            return $value;
        }

        if (is_callable($default)) {
            return call_user_func($default, $key);
        }

        return $default;
    }

    /**
     * Store an item in the cache indefinitely
     *
     * @param  string|array $key    The key to store the item under
     * @param  mixed        $value  The value of the item
     *
     * @return bool|array
     */
    public function forever($key, $value = null)
    {
        return $this->store($key, $value, 0);
    }


    /**
     * Check if the cache contains an item
     *
     * @param  string|array $key  The key (or keys) to search for
     *
     * @return bool|array
     */
    public function has($key)
    {
        if (is_array($key)) {
            $keys = $key;

            $has = [];
            foreach ($keys as $key) {
                $has[$key] = $this->has($key);
            }

            return $has;
        }

        return $this->driver->has($key);
    }


    /**
     * Fetch a value (or an multiple values) from the cache
     *
     * @param  string|array $key      The key (or keys) to retrieve the values of
     * @param  mixed        $default  Default value in case an item isn't found (can be a callback)
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            $keys = $key;
            $results = [];

            foreach ($keys as $key) {
                $results[$key] = $this->get($key, $default);
            }

            return $results;
        }

        if (isset($this->loaded[$key])) {
            return $this->loaded[$key];
        }

        $result = $this->driver->get($key);

        if (is_null($result)) {
            if (is_callable($default)) {
                return call_user_func($default, $key);
            }

            return $default;
        }

        return unserialize($result);
    }

    /**
     * Fetch a value (or multiple values), remove it from the cache and then return it
     *
     * @param  string|array $key      Key of the item to pull (can also be an array of keys)
     * @param  mixed        $default  Default value in case an item isn't found (can be a callback)
     *
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);

        $this->remove($key);

        return $value;
    }


    /**
     * Remove a value (or multiple values) from the cache
     *
     * @param  string|array $key  Key of the item to remove (can also be an array of keys)
     *
     * @return bool|array
     */
    public function remove($key)
    {
        if (is_array($key)) {
            $keys = $key;

            $success = [];
            foreach ($keys as $key) {
                $success[$key] = $this->remove($key);
            }

            return $success;
        }

        if (isset($this->loaded[$key])) {
            unset($this->loaded[$key]);
        }

        return $this->driver->remove($key);
    }

    /**
     * Clears the cache, removing ALL items
     *
     * @return bool
     */
    public function clear()
    {
        $this->loaded = [];

        return $this->driver->clear();
    }
}
