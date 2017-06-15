<?php

namespace J0sh0nat0r\SimpleCache;

use J0sh0nat0r\SimpleCache\Internal\PCI;

/**
 * The master cache class of SimpleCache
 *
 * Class Cache
 * @package J0sh0nat0r\SimpleCache
 */
class Cache
{
    /**
     *  Default storage time for cache items
     */
    const DEFAULT_TIME = 3600;


    /**
     * @var PCI Provides a property based cache interface
     */
    public $items;


    /**
     * @var IDriver
     */
    private $driver;
    /**
     * @var array
     */
    private $loaded = [];

    /**
     * Cache constructor.
     * @param $driver
     * @param null|array $driver_options
     */
    public function __construct($driver, $driver_options = null)
    {
        if(!is_null($driver_options))
            $this->driver = new $driver($driver_options);
        else
            $this->driver = new $driver();

        $this->items = new PCI($this);
    }

    /**
     * Store a value (or an array of key-value pairs) in the cache
     *
     * @param $key
     * @param null $value
     * @param int $time
     * @return bool
     * @throws \Exception
     */
    public function store($key, $value = null, $time = self::DEFAULT_TIME)
    {
        if (is_array($key)) {
            $time = is_null($value) ? $time : $value;
            $values = $key;
            $success = true;

            foreach ($values as $key => $value) {
                if (!$this->store($key, $value, $time)) {
                    $success = false;
                }
            }

            return $success;
        }

        if (is_null($value)) {
            throw new \Exception('Cannot store null');
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
     * Store an item in the cache indefinitely
     *
     * @param $key
     * @param null $value
     * @return bool
     */
    public function forever($key, $value = null)
    {
        return $this->store($key, $value, 0);
    }

    /**
     * Fetch a value (or an multiple values) from the cache
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            $keys = $key;
            $results = [];

            foreach ($keys as $key) {
                $results[$key] = $this->get($key);
            }

            return $results;
        }

        if (isset($this->loaded[$key])) {
            return $this->loaded[$key];
        }

        $result = $this->driver->get($key);

        if (is_null($result)) {
            if (is_callable($default)) {
                return $default();
            }
            return $default;
        }

        return unserialize($result);
    }

    /**
     * Try to find a value in the cache and return it,
     * if we can't it will be calculated with the provided closure
     *
     * @param string $key
     * @param int $time
     * @param \Closure $generate
     * @param mixed $default
     * @return array|mixed|null
     */
    public function remember($key, $time, $generate, $default = null)
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $value = $generate();
        if (!is_null($value)) {
            $this->store($key, $value, $time);
            return $value;
        }

        return $default;
    }

    /**
     * Check if the cache contains an item
     * TODO: Native support in drivers?
     *
     * @param string|array $key The key (or keys) to search for
     * @return bool
     */
    public function has($key)
    {
        if (is_array($key)) {
            $keys = $key;
            $has = true;

            foreach ($keys as $key) {
                if (!$this->has($key)) {
                    $has = false;
                }
            }

            return $has;
        }

        return !is_null($this->get($key));
    }

    /**
     * Fetch a value (or multiple values), remove it from the cache and then return it
     *
     * @param string|array $key
     * @param null $default
     * @return array|mixed|null
     */
    public function pull($key, $default = null)
    {
        if (is_array($key)) {
            $keys = $key;
            $results = [];

            foreach ($keys as $key) {
                $results[$key] = $this->pull($key);
            }

            return $results;
        }

        $result = $this->driver->get($key);

        if (is_null($result)) {
            return $default;
        }

        $this->remove($key);

        return unserialize($result);
    }

    /**
     * Remove a value (or multiple values) from the cache
     *
     * @param string|array $key
     * @return bool
     */
    public function remove($key)
    {
        if (is_array($key)) {
            $keys = $key;
            $success = true;

            foreach ($keys as $key) {
                if (!$this->remove($key)) {
                    $success = false;
                }
            }

            return $success;
        }

        if (isset($this->loaded[$key])) {
            unset($this->loaded[$key]);
        }

        return $this->driver->remove($key);
    }

    /**
     * Clear the cache
     *
     * @return bool
     */
    public function clear()
    {
        $this->loaded = [];

        return $this->driver->clear();
    }
}