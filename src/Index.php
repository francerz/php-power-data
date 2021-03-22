<?php

namespace Francerz\PowerData;

use ArrayAccess;
use Exception;

class Index implements ArrayAccess
{
    private $rows = [];
    private $columns = [];

    private $indexes = [];

    public function __construct(array $rows, array $columns)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->reindex();
    }

    public function reindex()
    {
        $this->indexes = array_fill_keys($this->columns, []);
        foreach ($this->rows as $k => $row) {
            $row = (array)$row;
            foreach ($this->columns as $col) {
                $v = array_key_exists($col, $row) ? $row[$col] : null;
                $this->indexes[$col][$v][] = $k;
            }
        }
    }

    public function findAllKeys(array $filter) : array
    {
        $keys = array_keys($this->rows);

        if (empty($filter)) return $keys;

        $ks = [];
        foreach ($filter as $k => $v) {
            $index = $this->indexes[$k];
            $ks[] = $index[$v] ?? [];
        }
        $keys = array_intersect($keys, ...$ks);

        return $keys;
    }

    public function findAll(array $filter) : array
    {
        if (empty($filter)) return $this->rows;

        $keys = $this->findAllKeys($filter);
        return array_intersect_key($this->rows, $keys);
    }

    public function offsetExists($offset)
    {
        if (is_array($offset)) {
            return count($this->findAllKeys($offset)) > 0;
        }
        return false;
    }

    public function offsetGet($offset)
    {
        if (is_array($offset)) {
            return $this->findAll($offset);
        }
        return [];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception("Read only collection.");
    }

    public function offsetUnset($offset)
    {
        throw new Exception("Read only collection.");
    }
}