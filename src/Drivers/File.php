<?php

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\SimpleCacheDriver;

class File extends SimpleCacheDriver
{
    private $items = [];
    private $dir;


    public function __construct($options)
    {
        if (!isset($options['dir'])) {
            throw new \Exception('No dir option passed for SimpleCache File driver');
        }

        $this->dir = $options['dir'];

        if (!file_exists($this->dir . '/items.json')) {
            $this->sync();
        } else {
            $this->items = json_decode(file_get_contents($this->dir . '/items.json'), true);
        }

        foreach ($this->items as $key => $item) {
            if ($item['expires_at'] && $item['expires_at'] <= time()) {
                $this->remove($key);
            }
        }
    }

    public function set($key, $value, $time)
    {
        $file_name = md5($value);

        $this->items[$key] = [
            'expires_at' => $time ? time() + $time : 0,
            'file_name' => $file_name
        ];

        file_put_contents($this->dir . '/' . $file_name, $value);

        return $this->sync();
    }

    public function get($key)
    {
        if (isset($this->items[$key])) {
            return file_get_contents($this->dir . '/' . $this->items[$key]['file_name']);
        }
        return null;
    }

    public function remove($key)
    {
        if (isset($this->items[$key])) {
            unlink($this->dir . '/' . $this->items[$key]['file_name']);
            unset($this->items[$key]);
            return $this->sync();
        }
        return true;
    }

    public function clear()
    {
        foreach (scandir($this->dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->dir . '/' . $file);
            }
        }

        $this->items = [];

        $this->sync();
    }

    private function sync()
    {
        if (file_put_contents($this->dir . '/items.json', json_encode($this->items))) {
            $this->items = json_decode(file_get_contents($this->dir . '/items.json'), true);
            return true;
        }
        return false;
    }
}