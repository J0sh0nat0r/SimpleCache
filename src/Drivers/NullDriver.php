<?php

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Null driver for unit tests
 *
 * @package J0sh0nat0r\SimpleCache\Drivers
 */
class NullDriver implements IDriver
{
    public function set($key, $value, $time)
    {
        return false;
    }

    public function has($key)
    {
        return false;
    }

    public function get($key)
    {
        return null;
    }

    public function remove($key)
    {
        return false;
    }

    public function clear()
    {
        return false;
    }
}