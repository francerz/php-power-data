<?php

namespace Francerz\PowerData;

class Multimap
{
    private $map;

    public function __construct()
    {
        $this->map = array();
    }

    public function add($key, ...$values)
    {
        $key = Map::transformKey($key);

        if (empty($this->map[$key])) {
            $this->map[$key] = array_unique($values);
        } else {
            $this->map[$key] = array_unique(array_merge($this->map[$key], $values));
        }
    }
    public function get($key)
    {
        $key = Map::transformKey($key);

        if (isset($this->map[$key])) {
            return $this->map[$key];
        }
        return [];
    }
    public function set($key, ...$values)
    {
        $key = Map::transformKey($key);

        $this->map[$key] = array_unique($values);
    }
}
