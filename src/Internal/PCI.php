<?php

namespace J0sh0nat0r\SimpleCache\Internal;

use J0sh0nat0r\SimpleCache\Cache;


/**
 * Property-Cache interface.
 *
 * @package J0sh0nat0r\SimpleCache\Internal
 */
class PCI
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * OCI constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $this->cache->store($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    function __isset($name)
    {
        return $this->cache->has($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->cache->get($name);
    }

    /**
     * @param $name
     */
    function __unset($name)
    {
        $this->cache->remove($name);
    }
}