<?php

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Driver;

class File extends Driver
{
    private $dir;


    public function __construct($options)
    {
        if (!isset($options['dir'])) {
            throw new \Exception('No dir option passed for SimpleCache File driver');
        }

        $this->dir = rtrim($options['dir'], '/');

        if(!is_dir($this->dir)) {
            mkdir($this->dir);
        }

        foreach (glob($this->dir . '/*', GLOB_ONLYDIR) as $item) {
            $data = json_decode(file_get_contents($item . '/data.json'), true);

            if($data['expiry'] > 0 && time() >= $data['expiry']) {
                $this->remove($data['key']);
            }
        }
    }

    public function set($key, $value, $time)
    {
        try {
            $dir = $this->dir . '/' . sha1($key);

            if (!file_exists($dir)) {
                mkdir($dir);
            }

            $expiry = $time > 0 ? time() + $time : 0;

            file_put_contents($dir . '/data.json', json_encode(['expiry' => $expiry, 'key' => $key]));
            file_put_contents($dir . '/item.dat', $value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function get($key)
    {
        $dir = $this->dir . '/' . sha1($key);

        if(!is_dir($dir)) {
            return null;
        }

        return file_get_contents($dir . '/item.dat');
    }

    public function remove($key)
    {
        try {
            $dir = $this->dir . '/' . sha1($key);

            if (!is_dir($dir)) {
                return false;
            }

            unlink($dir . '/data.json');
            unlink($dir . '/item.dat');
            rmdir($dir);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function clear()
    {
        foreach (glob($this->dir . '/*', GLOB_ONLYDIR) as $item) {
            $data = json_decode(file_get_contents($item . '/data.json'), true);
            $this->remove($data['key']);
        }
    }
}