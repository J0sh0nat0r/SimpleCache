<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Internal;

use J0sh0nat0r\SimpleCache\Cache;

/**
 * Property-Cache interface.
 */
class PCI
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * OCI constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $name
     *
     * @return bool
     * @throws \J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException
     */
    public function __isset($name)
    {
        return $this->cache->has($name);
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException
     */
    public function __get($name)
    {
        return $this->cache->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException
     */
    public function __set($name, $value)
    {
        $this->cache->store($name, $value);
    }

    /**
     * @param $name
     * @throws \J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException
     */
    public function __unset($name)
    {
        $this->cache->remove($name);
    }
}
