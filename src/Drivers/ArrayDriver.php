<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Array driver for unit tests
 *
 * Accepted options: None
 *
 * @package J0sh0nat0r\SimpleCache\Drivers
 */
class ArrayDriver implements IDriver
{
    /**
     * @var  array[] $items
     */
    private $items;


    public function set($key, $value, $time)
    {
        $items[$key] = [
            'value' => $value,
            'expiry' => time() + $time
        ];
    }

    public function has($key)
    {
        if (!isset($this->items[$key])) {
            return false;
        }

        $item = $this->items[$key];

        if ($item['expiry'] !== 0 && $item['expiry'] < time()) {
            $this->remove($key);
            return false;
        }

        return true;
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        return $this->items[$key];
    }

    public function remove($key)
    {
        unset($this->items[$key]);
        return true;
    }

    public function clear()
    {
        $this->items = [];
    }
}
