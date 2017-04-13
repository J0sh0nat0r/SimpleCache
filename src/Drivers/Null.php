<?php

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Driver;

/**
 * Null driver for unit testing
 *
 * Class Null
 * @package J0sh0nat0r\SimpleCache\Drivers
 */
class Null extends Driver
{
    public function __construct($options)
    {
    }

    public function set($key, $value, $time)
    {
        return true;
    }

    public function get($key)
    {
        return null;
    }

    public function remove($key)
    {
        return true;
    }

    public function clear()
    {
        return true;
    }
}