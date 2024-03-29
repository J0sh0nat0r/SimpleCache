<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Drivers;

use Closure;
use Exception;
use J0sh0nat0r\SimpleCache\Exceptions\DriverInitializationFailedException;
use J0sh0nat0r\SimpleCache\Exceptions\DriverOptionsInvalidException;
use J0sh0nat0r\SimpleCache\IDriver;
use JsonException;
use RuntimeException;

/**
 * File driver.
 *
 * Accepted options:
 *   * dir:            (required) The directory to store cache files in.
 *   * encryption_key: (optional) If specified, will be used to encrypt data.
 */
class File implements IDriver
{
    /**
     * Directory in which to store items.
     *
     * @var string
     */
    private string $dir;

    /**
     * Key to use when encrypting item data.
     *
     * @var string
     */
    private $encryption_key;

    /**
     * Determines whether or not to encrypt item data,
     * basically a shortcut for `!empty($this->encryption_key).
     *
     * @var bool
     */
    private bool $encrypt_data = false;

    /**
     * File driver constructor.
     *
     * @param $options
     *
     * @throws DriverInitializationFailedException
     * @throws DriverOptionsInvalidException
     */
    public function __construct($options)
    {
        if (!isset($options['dir'])) {
            throw new DriverOptionsInvalidException('The dir option is required');
        }

        if (!is_string($options['dir'])) {
            throw new DriverOptionsInvalidException('The dir option must be a string');
        }

        $this->dir = rtrim($options['dir'], '/');
        if (!is_dir($this->dir) && !mkdir($concurrentDirectory = $this->dir) && !is_dir($concurrentDirectory)) {
            throw new DriverInitializationFailedException(
                'Cache directory does not exist and automatic creation failed'
            );
        }

        if (isset($options['encryption_key'])) {
            if (!is_string($options['encryption_key'])) {
                throw new DriverOptionsInvalidException('The encryption_key option must be a string');
            }

            $this->encrypt_data = true;
            $this->encryption_key = hash('sha256', $options['encryption_key']);
        }

        $this->forAll(function ($item) {
            if (!$this->isValid($item)) {
                if (!$this->delDir($item)) {
                    throw new DriverInitializationFailedException(
                        'Failed to remove invalid item! Please manually delete: '.$item
                    );
                }

                return;
            }

            $data = json_decode(
                file_get_contents($item.'/data.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if ($this->expired($data['key'])) {
                $this->remove($data['key']);
            }
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function put(string $key, $value, $time): bool
    {
        $encrypted = $this->encrypt_data;
        $expiry = $time > 0 ? time() + $time : null;

        $item_data = compact('key', 'expiry', 'encrypted');

        if ($encrypted) {
            $value = $this->encrypt($value, $iv);

            if ($value === false) {
                throw new Exception('Failed to encrypt item: '.openssl_error_string());
            }

            $item_data['iv'] = $iv;
        }

        if ($this->has($key)) {
            if (!$this->remove($key)) {
                throw new Exception('Failed to remove pre-existing version of an item with the key: '.$key);
            }
        }

        $dir = $this->getDir($key);
        if (!is_dir($dir)) {
            if (!mkdir($dir) && !is_dir($dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

        $success = file_put_contents($dir.'/data.json', json_encode($item_data, JSON_THROW_ON_ERROR));
        $success = file_put_contents($dir.'/item.dat', $value) ? $success : false;

        return (bool) $success;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
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

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function get(string $key): ?string
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
                throw new Exception('Item is encrypted but no encryption key was provided');
            }

            $value = $this->decrypt($value, $data['iv']);

            if ($value === false) {
                throw new Exception('Failed to decrypt item: '.openssl_error_string());
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        $dir = $this->getDir($key);

        if (!is_dir($dir)) {
            return true;
        }

        return $this->delDir($this->getDir($key));
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $success = true;

        $this->forAll(function ($item) use (&$success) {
            if (!$this->delDir($item)) {
                $success = false;
            }
        });

        return $success;
    }

    /**
     * Generates a directory based on an item's key.
     *
     * @param string $key Key of the item to generate a directory for
     *
     * @return string Directory for the given key
     */
    private function getDir(string $key): string
    {
        return $this->dir.'/'.sha1($key);
    }

    /**
     * Recursively deletes a directory.
     *
     * @param string $directory Directory to delete
     *
     * @return bool TRUE on success, FALSE on failure
     */
    private function delDir(string $directory): bool
    {
        $success = true;
        $contents = array_slice(scandir($directory), 2);

        foreach ($contents as $value) {
            $path = $directory.DIRECTORY_SEPARATOR.$value;

            if (is_file($path)) {
                $success = unlink($path) ? $success : false;
            } else {
                $success = $this->delDir($path) ? $success : false;
            }
        }

        return rmdir($directory) ? $success : false;
    }

    /**
     * Tests the validity of an item.
     *
     * @param string $dir Dir of the item who's validity we should check
     *
     * @return bool
     */
    private function isValid(string $dir): bool
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
    private function getData(string $key): ?array
    {
        $dir = $this->getDir($key);

        if (!$this->isValid($dir)) {
            return null;
        }

        return json_decode(file_get_contents($dir.'/data.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Calls callback on each item in the cache.
     *
     * @param Closure $callback Callback
     */
    private function forAll(Closure $callback): void
    {
        foreach (glob($this->dir.'/*', GLOB_ONLYDIR) as $item) {
            if ($this->isValid($item)) {
                $callback($item);
            }
        }
    }

    /**
     * Checks if an item is expired.
     *
     * @param string $key Key of the item to check
     *
     * @throws JsonException
     *
     * @return bool True if the item is expired, otherwise, false
     */
    private function expired(string $key): bool
    {
        $data = $this->getData($key);

        return !is_null($data['expiry']) && $data['expiry'] <= time();
    }

    /**
     * Encrypts a string with the encryption key and provided initialisation vector.
     *
     * @param string  $data String to encrypt
     * @param ?string &$iv  Encryption initialization vector (out)
     *
     * @return string
     */
    private function encrypt(string $data, ?string &$iv): string
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
    private function decrypt(string $data, string $iv): string
    {
        return openssl_decrypt($data, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }
}
