<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * APC Driver.
 *
 * Accepted options: None!
 */
class APC implements IDriver
{
    /**
     * APC prefix (apcu_ or apc_).
     *
     * @var string
     */
    private string $prefix;

    /**
     * APC constructor.
     *
     * @throws DriverInitializationFailedException
     */
    public function __construct()
    {
        if (extension_loaded('apcu')) {
            $this->prefix = 'apcu_';
        } elseif (extension_loaded('apc')) {
            $this->prefix = 'apc_';
        } else {
            throw new DriverInitializationFailedException('This driver requires APC or APCu');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $key, $value, $time): bool
    {
        $function = $this->prefix . 'store';

        return $function($key, $value, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        $function = $this->prefix . 'exists';

        return $function($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?string
    {
        $function = $this->prefix . 'fetch';

        $success = false;
        $result = $function($key, $success);

        if (!$success) {
            return null;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        $function = $this->prefix . 'delete';

        return $function($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        if ($this->prefix === 'apc_') {
            return apc_clear_cache('user');
        }

        return apcu_clear_cache();
    }
}
