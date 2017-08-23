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
     */
    public function __isset($name)
    {
        return $this->cache->has($name);
    }

    /**
     * @param $name
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
     */
    public function __set($name, $value)
    {
        $this->cache->store($name, $value);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $this->cache->remove($name);
    }
}
