<?php

namespace Francerz\PowerData;

use Countable;
use Iterator;

class SortedIndex implements Countable, Iterator
{
    private $list;
    private $sorted = false;

    public function __construct(iterable $list)
    {
        $this->list = Arrays::fromIterable($list);
    }
    private function sort()
    {
        sort($this->list);
        $this->sorted = true;
    }
    public function contains($value, ?int &$jumps = null)
    {
        if (!$this->sorted) {
            $this->sort();
        }
        $jumps = 0;
        return static::binarySearch($this->list, $value, $jumps) !== null;
    }
    public function count()
    {
        return count($this->list);
    }

    public function current()
    {
        return current($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function next()
    {
        return next($this->list);
    }

    public function valid()
    {
        return key($this->list) !== null;
    }

    public function rewind()
    {
        return reset($this->list);
    }

    private static function binarySearch(array $array, $value, int &$jumps = 0)
    {
        if (count($array) === 0) return null;
        $start = 0;
        $end = count($array) - 1;

        while ($start <= $end) {
            $jumps++;
            $mid = intval(($start + $end) / 2);
            $chk = $array[$mid];
            if ($chk < $value) {
                $start = $mid + 1;
            } elseif ($chk > $value) {
                $end = $mid - 1;
            } else {
                return $mid;
            }
        }
        return null;
    }

    public static function intersect(SortedIndex ... $indexes)
    {
        usort($indexes, function($a, $b) { return count($a)-count($b); });
        $first = array_shift($indexes);
        $base = $first->list;
        foreach ($indexes as $index) {
            $newBase = [];
            foreach ($base as $val) {
                if ($index->contains($val)) $newBase[] = $val;
            }
            $base = $newBase;
        }
        return array_values($base);
    }

    private static $_empty;
    public static function newEmpty() : SortedIndex
    {
        if (!isset(static::$_empty)) {
            static::$_empty = new static([]);
        }
        return static::$_empty;
    }
}