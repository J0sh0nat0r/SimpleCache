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
 * dir -            The directory to store cache files in
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
            throw new DriverOptionsInvalidException('Missing option: dir');
        }

        $this->dir = rtrim($options['dir'], '/');
        if (!is_dir($this->dir)) {
            if (!mkdir($this->dir)) {
                throw new \Exception('Cache directory does not exist and automatic creation failed');
            }
        }

        $this->encrypt_data = isset($options['encryption_key']);
        if ($this->encrypt_data) {
            $this->encryption_key = hash('sha256', $options['encryption_key']);
        }

        $this->forAll(function ($item) {
            if (!$this->isValid($item)) {
                @unlink($item.'/data.json');
                @unlink($item.'/item.dat');

                if (!rmdir($item)) {
                    throw new \Exception('Failed to remove invalid item! Please manually delete: '.$item);
                }

                return;
            }

            $data = json_decode(file_get_contents($item.'/data.json'), true);

            if ($this->expired($data['key'])) {
                $this->remove($data['key']);
            }
        });
    }

    public function set($key, $value, $time)
    {
        $dir = $this->getDir($key);

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $encrypted = $this->encrypt_data;
        $expiry = $time > 0 ? time() + $time : null;

        $item_data = compact('key', 'expiry', 'encrypted');

        if ($encrypted) {
            $value = $this->encrypt($value, $iv);

            if ($value === false) {
                throw new \Exception('Failed to encrypt item: '.openssl_error_string());
            }

            $item_data['iv'] = $iv;
        }

        if ($this->has($key)) {
            if (!$this->remove($key)) {
                throw new \Exception('Failed to remove pre-existing version of an item with the key: '.$key);
            }
        }

        $success = file_put_contents($dir.'/data.json', json_encode($item_data));
        $success = file_put_contents($dir.'/item.dat', $value) ? $success : false;

        return boolval($success);
    }

    public function has($key)
    {
        $dir = $this->getDir($key);

        if (!$this->isValid($dir)) {
            return false;
        }

        if ($this->expired($key)) {
            return false;
        }

        return true;
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        $data = $this->getData($key);

        $value = file_get_contents($this->getDir($key).'/item.dat');

        if ($value === false) {
            return null;
        }

        if ($data['encrypted']) {
            if (!$this->encrypt_data) {
                throw new \Exception('Item is encrypted but no encryption key was provided');
            }

            $value = $this->decrypt($value, $data['iv']);

            if ($value === false) {
                throw new \Exception('Failed to decrypt item: '.openssl_error_string());
            }
        }

        return $value;
    }

    public function remove($key)
    {
        $dir = $this->getDir($key);

        @unlink($dir.'/data.json');
        @unlink($dir.'/item.dat');

        return rmdir($dir);
    }

    public function clear()
    {
        $this->forAll(function ($item) {
            $data = json_decode(file_get_contents($item.'/data.json'), true);

            $this->remove($data['key']);
        });
    }

    /**
     * Generates a directory based on an item's key.
     *
     * @param string $key Key of the item to generate a directory for
     *
     * @return string
     */
    private function getDir($key)
    {
        return $this->dir.'/'.sha1($key);
    }

    /**
     * Tests the validity of an item.
     *
     * @param string $dir Dir of the item who's validity we should check
     *
     * @return bool
     */
    private function isValid($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $valid = file_exists($dir.'/data.json');
        $valid = file_exists($dir.'/item.dat') ? $valid : false;

        return $valid;
    }

    /**
     * Retrieves an item's data from disk.
     *
     * @param string $key Key of the item who's data we're retrieving
     *
     * @return array|null
     */
    private function getData($key)
    {
        $dir = $this->getDir($key);

        if (!$this->isValid($dir)) {
            return null;
        }

        return json_decode(file_get_contents($dir.'/data.json'), true);
    }

    /**
     * Calls callback on each item in the cache.
     *
     * @param \Closure $callback Callback
     */
    private function forAll($callback)
    {
        foreach (glob($this->dir.'/*', GLOB_ONLYDIR) as $item) {
            $callback($item);
        }
    }

    /**
     * Checks if an item is expired.
     *
     * @param string $key Key of the item to check
     *
     * @return bool True if the item is expired, otherwise, false
     */
    private function expired($key)
    {
        $data = $this->getData($key);

        if (!is_null($data['expiry'])) {
            if ($data['expiry'] <= time()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Encrypts a string with the encryption key and provided initialisation vector.
     *
     * @param string $data String to encrypt
     * @param string $iv Encryption initialization vector (out)
     *
     * @return string
     */
    private function encrypt($data, &$iv)
    {
        $iv = bin2hex(openssl_random_pseudo_bytes(8));

        return openssl_encrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }

    /**
     * Decrypts a string with the encryption key and provided initialisation vector.
     *
     * @param string $data String to decrypt
     * @param string $iv   The initialisation vector used to encrypt the item
     *
     * @return string
     */
    private function decrypt($data, $iv)
    {
        return openssl_decrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }
}
