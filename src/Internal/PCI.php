<?php
/**
 * Copyright (c) 2017 Josh P (joshp.xyz).
 */

namespace J0sh0nat0r\SimpleCache\Internal;

use J0sh0nat0r\SimpleCache\Cache;
use J0sh0nat0r\SimpleCache\Exceptions\InvalidKeyException;

/**
 * Property-Cache interface.
 * 
 * @internal
 */
class PCI implements \ArrayAccess
{
    /**
     * @var Cache
     */
    private Cache $cache;

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
     * @throws InvalidKeyException
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->cache->has($name);
    }

    /**
     * @param $name
     *
     * @throws InvalidKeyException
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->cache->get($name);
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws InvalidKeyException
     */
    public function __set($name, $value)
    {
        $this->cache->store($name, $value);
    }

    /**
     * @param $name
     *
     * @throws InvalidKeyException
     */
    public function __unset($name)
    {
        $this->cache->remove($name);
    }

    /**
     * @throws InvalidKeyException
     */
    public function offsetExists($offset)
    {
        return $this->cache->has($offset);
    }

    /**
     * @throws InvalidKeyException
     */
    public function offsetGet($offset)
    {
        return $this->cache->get($offset);
    }

    /**
     * @throws InvalidKeyException
     */
    public function offsetSet($offset, $value)
    {
        $this->cache->store($offset, $value);
    }

    /**
     * @throws InvalidKeyException
     */ 
    public function offsetUnset($offset)
    {
        $this->cache->remove($offset);
    }
}
