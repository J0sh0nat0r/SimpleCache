<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;

/**
 * File driver.
 *
 * Accepted options:
 * dir - The directory to store cache files in
 * encryption_key - The key to use for encrypting data, if not set data will NOT be encrypted
 */
class File implements IDriver
{
    private $dir;
    private $encrypt_data;
    private $encryption_key;

    public function __construct($options)
    {
        if (!isset($options['dir'])) {
            throw new DriverOptionsInvalidException('No dir option passed for SimpleCache File driver');
        }

        $this->dir = rtrim($options['dir'], '/');
        if (!is_dir($this->dir)) {
            mkdir($this->dir);
        }

        if (isset($options['encryption_key'])) {
            $this->encrypt_data = true;
            $this->encryption_key = $options['encryption_key'];
        }

        foreach (glob($this->dir.'/*', GLOB_ONLYDIR) as $item) {
            $data = json_decode(file_get_contents($item.'/data.json'), true);

            if ($data['expiry'] > 0 && time() >= $data['expiry']) {
                $this->remove($data['key']);
            }
        }
    }

    public function set($key, $value, $time)
    {
        try {
            $dir = $this->dir.'/'.sha1($key);

            if (!is_dir($dir)) {
                mkdir($dir);
            }


            $expiry = $time > 0 ? time() + $time : 0;

            $item = [
                'key'       => $key,
                'expiry'    => $expiry,
                'encrypted' => $this->encrypt_data,
            ];

            if ($this->encrypt_data) {
                $value = $this->encrypt($value, $iv);
                $item['iv'] = $iv;
            }

            file_put_contents($dir.'/data.json', json_encode($item));
            file_put_contents($dir.'/item.dat', $value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function has($key)
    {
        return is_dir($this->dir.'/'.sha1($key));
    }

    public function get($key)
    {
        $dir = $this->dir.'/'.sha1($key);

        if (is_dir($dir)) {
            $data = json_decode(file_get_contents($dir.'/data.json'), true);

            if ($data['expiry'] <= time()) {
                $this->remove($key);

                return null;
            }

            if ($this->encrypt_data) {
                if ($data['encrypted']) {
                    return $this->decrypt(file_get_contents($dir.'/item.dat'), $data['iv']);
                }
            }

            return file_get_contents($dir.'/item.dat');
        }

        return null;
    }

    public function remove($key)
    {
        try {
            $dir = $this->dir.'/'.sha1($key);

            if (!is_dir($dir)) {
                return false;
            }

            unlink($dir.'/data.json');
            unlink($dir.'/item.dat');
            rmdir($dir);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function clear()
    {
        foreach (glob($this->dir.'/*', GLOB_ONLYDIR) as $item) {
            $data = json_decode(file_get_contents($item.'/data.json'), true);
            $this->remove($data['key']);
        }
    }

    private function encrypt($data, &$iv)
    {
        $iv = bin2hex(openssl_random_pseudo_bytes(6));

        return openssl_encrypt($data, 'aes-256-gcm', $this->encryption_key, 0, $iv);
    }

    private function decrypt($data, $iv)
    {
        return openssl_decrypt($data, 'aes-256-gcm', $this->encryption_key, 0, $iv);
    }
}
