<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\IDriver;

/**
 * Array driver for unit tests.
 *
 * Accepted options: None!
 */
class ArrayDriver implements IDriver
{
    /**
     * Array containing stored items.
     *
     * @var array[]
     */
    private array $items;

    /**
     * {@inheritdoc}
     */
    public function put(string $key, $value, $time): bool
    {
        $this->items[$key] = [
            'value' => $value,
            'expiry' => $time > 0 ? time() + $time : null,
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        if (!isset($this->items[$key])) {
            return false;
        }

        $item = $this->items[$key];

        if (!is_null($item['expiry']) && $item['expiry'] <= time()) {
            $this->remove($key);

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?string
    {
        if ($this->has($key)) {
            return $this->items[$key]['value'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        unset($this->items[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->items = [];

        return true;
    }
}
