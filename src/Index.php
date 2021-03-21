<?php

namespace Francerz\PowerData;

class Index
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

        foreach ($filter as $k => $v) {
            $index = $this->indexes[$k];
            $ks = $index[$v] ?? [];
            $keys = array_intersect($keys, $ks);
        }

        return array_values($keys);
    }

    public function findAll(array $filter) : array
    {
        if (empty($filter)) return $this->rows;

        $keys = $this->findAllKeys($filter);
        return array_intersect_key($this->rows, $keys);
    }
}