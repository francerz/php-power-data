<?php

namespace Francerz\PowerData;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use LogicException;

class Index implements ArrayAccess, Countable, Iterator
{
    private $rows = [];
    private $columns = [];

    private $indexes = [];

    /**
     * Creates an Indexed iterable, array access and countable object
     *
     * @param iterable $iterable Rows that should be indexed.
     * @param array $columns Columns that match the indexes.
     */
    public function __construct($iterable, array $columns)
    {
        $this->rows = Arrays::fromIterable($iterable);
        $this->columns = $columns;
        $this->reindex();
    }

    private function indexColumn(string $col)
    {
        if (array_key_exists($col, $this->columns)) {
            throw new Exception(sprintf('Column %s already exists.', $col));
        }
        $idxCol = [];
        foreach ($this->rows as $k => $row) {
            $row = (object)$row;
            $v = $row->{$col} ?? null;
            $idxCol[$v] = $idxCol[$v] ?? new SortedIndex();
            $idxCol[$v][$k] = $k;
        }
        $this->indexes[$col] = $idxCol;
    }

    /**
     * @param object $row
     * @param int $k
     * @param array &$indexes
     * @return void
     */
    private function indexRow(object $row, $k, ?array &$indexes = null)
    {
        foreach ($this->columns as $col) {
            $v = $row->{$col} ?? null;
            $indexes[$col][$v] = $indexes[$col][$v] ?? new SortedIndex();
            $indexes[$col][$v][$k] = $k;
        }
    }

    /**
     * Rebuilds the indexes.
     *
     * Using this inside a loop might cause slow performance.
     *
     * @return void
     */
    public function reindex()
    {
        $indexes = array_fill_keys($this->columns, []);
        foreach ($this->rows as $k => $row) {
            $this->indexRow((object)$row, $k, $indexes);
        }
        foreach ($indexes as &$col) {
            foreach ($col as &$v) {
                $v = new SortedIndex($v);
            }
        }
        $this->indexes = $indexes;
    }

    /**
     * Adds an item and index it.
     *
     * @param mixed $item
     * @return void
     */
    public function add($item)
    {
        if (!is_object($item) && !is_array($item)) {
            throw new Exception(sprintf('Invalid $item type %s, only object or array accepted.', gettype($item)));
        }
        $this->rows[] = $item;
        $keys = array_keys($this->rows);
        $k = end($keys);
        $row = (object)$item;
        $this->indexRow($row, $k, $this->indexes);
    }

    public function addColumn(string $column)
    {
        $this->indexColumn($column);
        $this->columns[] = $column;
    }

    /**
     * @param array $filter
     * @return array
     */
    public function findAllKeys(array $filter)
    {
        if (empty($filter)) {
            return array_keys($this->rows);
        }

        $ks = [];
        foreach ($filter as $k => $v) {
            if (!isset($this->indexes[$k])) {
                throw new LogicException("Missing '{$k}' filter index.");
            }

            $index = $this->indexes[$k];

            if (is_array($v)) {
                $idx = [];
                foreach ($v as $val) {
                    $data = isset($index[$val]) ? $index[$val]->toArray() : [];
                    $idx = array_merge($idx, $data);
                }
                $ks[] = new SortedIndex($idx);
                continue;
            }

            if (!isset($index[$v])) {
                return [];
            }
            $ks[] = $index[$v];
        }

        if (count($ks) < 2) {
            $ret = reset($ks);
            return Arrays::fromIterable($ret);
        }

        return call_user_func([SortedIndex::class, 'intersect'], $ks);
    }

    public function findAll(array $filter)
    {
        if (empty($filter)) {
            return $this->rows;
        }

        return array_intersect_key($this->rows, array_flip($this->findAllKeys($filter)));
    }

    #[\ReturnTypeWillChange]
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

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (is_array($offset)) {
            return $this->findAll($offset);
        }
        if (is_int($offset) || is_string($offset)) {
            return $this->rows[$offset] ?? null;
        }
        return [];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (!is_null($offset)) {
            throw new Exception('Unsupported $offset set.');
        }
        $this->add($value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new Exception("Read only collection.");
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->rows);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->rows);
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return key($this->rows) !== null;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->rows);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->rows);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->rows);
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumnValues(string $column, ?array $filter = null)
    {
        $values = [];
        if (isset($filter)) {
            $rows = $this->findAll($filter);
            $values = array_column($rows, $column);
        } elseif (array_key_exists($column, $this->indexes)) {
            $values = array_keys($this->indexes[$column]);
        } else {
            $values = array_column($this->rows, $column);
        }
        return array_unique($values);
    }

    public function groupBy(string $column)
    {
        $groups = [];
        foreach ($this->getColumnValues($column) as $v) {
            $groups[$v] = $this[[$column => $v]];
        }
        return $groups;
    }

    /**
     * Performs aggregation to indexed elements by a given callback function.
     *
     * If $column is set, then aggregation will be performed by specified column.
     * When $column is null, then aggregation will consider full indexed items.
     *
     * if $filter is set, collection will be filtered by given restrictions.
     *
     * @param callable $method Callback function to aggregate.
     * @param string|null $column Column to aggregate, if null items will be passed.
     * @param array|null $filter Filters indexed items.
     * @return mixed
     */
    public function aggregate(callable $callback, ?string $column = null, ?array $filter = null, array $moreArgs = [])
    {
        $items = isset($filter) ? $this[$filter] : $this->rows;
        $items = isset($column) ? array_column($items, $column) : $items;
        return empty($items) ? null : call_user_func($callback, $items, ...$moreArgs);
    }

    public function first(?array $filter = null, ?string $column = null)
    {
        return $this->aggregate([Aggregations::class, 'first'], $column, $filter);
    }

    public function last(?array $filter = null, ?string $column = null)
    {
        return $this->aggregate([Aggregations::class, 'last'], $column, $filter);
    }

    public function xCount(?array $filter = null, ?string $column = null, bool $ignoreNulls = false)
    {
        return $this->aggregate([Aggregations::class, 'count'], $column, $filter, [$ignoreNulls]);
    }

    public function countDistinct(?array $filter = null, ?string $column = null, bool $ignoreNulls = false)
    {
        return $this->aggregate([Aggregations::class, 'countDistinct'], $column, $filter, [$ignoreNulls]);
    }

    public function sum(?string $column = null, ?array $filter = null)
    {
        return $this->aggregate([Aggregations::class, 'sum'], $column, $filter);
    }
}
