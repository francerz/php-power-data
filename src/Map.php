<?php

namespace Francerz\PowerData;

use Iterator;

class Map implements Iterator
{
    private array $map;
    public function __construct()
    {
        $this->map = array();
    }

    static public function transformKey($key)
    {
        if (is_array($key)) {
            throw new PowerDataException('Array is not a valid key');
        }
        if (is_object($key)) {
            return Objects::getId($key);
        }
        return $key;
    }

    public function add($key, $value, $overwrite = false)
    {
        $key = static::transformKey($key);

        if (array_key_exists($key, $this->map) && !$overwrite) {
            throw new PowerDataException("Map->add(): Trying to overwrite existing item");
        }

        $this->map[$key] = $value;
    }

    public function get($key)
    {
        $key = static::transformKey($key);

        if (isset($this->map[$key])) {
            return $this->map[$key];
        }
        return null;
    }

    public function set($key, $value)
    {
        $key = static::transformKey($key);

        $this->map[$key] = $value;
    }

    public function remove($key)
    {
        $key = static::transformKey($key);

        unset($this->map[$key]);
    }

    public function rewind()
    {
        return reset($this->map);
    }
    public function current()
    {
        return current($this->map);
    }
    public function key()
    {
        return key($this->map);
    }
    public function valid()
    {
        return key($this->map) !== null;
    }
    public function next()
    {
        return next($this->map);
    }
}