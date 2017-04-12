<?php

namespace J0sh0nat0r\SimpleCache;

abstract class Driver
{
    abstract public function set($key, $value, $time);

    abstract public function get($key);

    abstract public function remove($key);

    abstract public function clear();
}