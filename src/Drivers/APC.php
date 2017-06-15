<?php

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\IDriver;

class APC implements IDriver
{
    private $prefix;


    public function __construct()
    {
        if (extension_loaded('apc')) {
            $this->prefix = 'apc_';
        } elseif (extension_loaded('apcu')) {
            $this->prefix = 'apcu_';
        } else {
            throw new \Exception('SimpleCache APC driver requires APC or APCu');
        }
    }

    public function set($key, $value, $time)
    {
        return ($this->prefix . 'store')($key, $value, $time);
    }

    public function get($key)
    {
        $success = false;

        $result = ($this->prefix . 'fetch')($key, $success);

        if (!$success) {
            return null;
        }

        return $result;
    }

    public function remove($key)
    {
        return ($this->prefix . 'delete')($key);
    }

    public function clear()
    {
        if ($this->prefix === 'apc_') {
            return apc_clear_cache('user');
        }
        return apcu_clear_cache();
    }
}