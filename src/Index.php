<?php

namespace Francerz\PowerData;

use ArrayAccess;
use Countable;
use Exception;
use JsonSerializable;

class Index implements ArrayAccess, Countable
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

    private function indexColumn(string $col)
    {
        if (array_key_exists($col, $this->columns)) {
            throw new Exception(sprintf('Column %s already exists.', $col));
        }
        foreach ($this->rows as $k => $row) {
            $row = (array)$row;
            $v = array_key_exists($col, $row) ? $row[$col] : null;
            $this->indexes[$col][$v][] = $k;
        }
        $this->columns[] = $col;
    }

    private function indexRow(array $row, int $k)
    {
        foreach ($this->columns as $col) {
            $v = array_key_exists($col, $row) ? $row[$col] : null;
            $this->indexes[$col][$v][] = $k;
        }
    }

    public function reindex()
    {
        $this->indexes = array_fill_keys($this->columns, []);
        foreach ($this->rows as $k => $row) {
            $this->indexRow((array)$row, $k);
        }
    }

    public function add($item)
    {
        if (!is_object($item) && !is_array($item)) {
            throw new Exception(sprintf('Invalid $item type %s, only object or array accepted.', gettype($item)));
        }
        $this->rows[] = $item;
        $k = array_key_last($this->rows);
        $row = (array)$item;
        $this->indexRow($row, $k);
    }

    public function addColumn(string $column)
    {
        $this->indexColumn($column);
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
        if (is_int($offset)) {
            return array_key_exists($offset, $this->rows) ?
                $this->rows[$offset] : null;
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
        if (!is_null($offset)) {
            throw new Exception('Unsupported $offset set.');
        }
        $this->add($value);
    }

    public function offsetUnset($offset)
    {
        throw new Exception("Read only collection.");
    }

    public function count()
    {
        return count($this->rows);
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumnValues(string $column)
    {
        if (!array_key_exists($column, $this->indexes)) {
            return [];
        }
        return array_unique(array_keys($this->indexes[$column]));
    }
}