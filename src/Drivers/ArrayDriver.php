<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Array driver for unit tests.
 *
 * Accepted options: None
 */
class ArrayDriver implements IDriver
{
    /**
     * @var array[]
     */
    private $items;

    public function set($key, $value, $time)
    {
        $this->items[$key] = [
            'value'  => $value,
            'expiry' => $time > 0 ? time() + $time : null,
        ];

        return true;
    }

    public function has($key)
    {
        if (!isset($this->items[$key])) {
            return false;
        }

        $item = $this->items[$key];

        if (!is_null($item['expiry'])) {
            if ($item['expiry'] <= time()) {
                $this->remove($key);

                return false;
            }
        }

        return true;
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->items[$key]['value'];
        }

        return null;
    }

    public function remove($key)
    {
        unset($this->items[$key]);

        return true;
    }

    public function clear()
    {
        $this->items = [];

        return true;
    }
}
