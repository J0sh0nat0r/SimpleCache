<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * APC Driver.
 *
 * Accepted options: None
 */
class APC implements IDriver
{
    /**
     * @var string APC prefix (apcu_ or apc_)
     */
    private $prefix;

    public function __construct()
    {
        if (extension_loaded('apcu')) {
            $this->prefix = 'apcu_';
        } elseif (extension_loaded('apc')) {
            $this->prefix = 'apc_';
        } else {
            throw new DriverOptionsInvalidException('This driver requires APC or APCu');
        }
    }

    public function set($key, $value, $time)
    {
        return ($this->prefix.'store')($key, $value, $time);
    }

    public function has($key)
    {
        return ($this->prefix.'exists')($key);
    }

    public function get($key)
    {
        $success = false;

        $result = ($this->prefix.'fetch')($key, $success);

        if (!$success) {
            return null;
        }

        return $result;
    }

    public function remove($key)
    {
        return ($this->prefix.'delete')($key);
    }

    public function clear()
    {
        if ($this->prefix === 'apc_') {
            return apc_clear_cache('user');
        }

        return apcu_clear_cache();
    }
}
