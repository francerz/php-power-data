<?php

namespace Francerz\PowerData;

use ArrayAccess;
use Countable;
use Iterator;

class SortedIndex implements Countable, Iterator, ArrayAccess, ArrayableInterface
{
    private $list;
    private $sorted = false;

    /**
     * @param iterable $list
     */
    public function __construct($list = [])
    {
        $this->list = Arrays::fromIterable($list);
    }

    private function sort()
    {
        sort($this->list);
        $this->sorted = true;
    }

    public function toArray(): array
    {
        if (!$this->sorted) {
            $this->sort();
        }
        return $this->list;
    }

    /**
     * @param mixed $value
     * @param integer|null $jumps
     * @return void
     */
    public function contains($value, &$jumps = null)
    {
        $jumps = 0;
        return $this->binarySearch($value, $jumps) !== null;
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->list);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->list);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->list);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        return next($this->list);
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return key($this->list) !== null;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->list);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->contains($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->binarySearch($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->list[$offset] = $value;
        $this->sorted = false;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
        $this->sorted = false;
    }

    /**
     * @param mixed $value
     * @param integer $jumps
     * @return void
     */
    private function binarySearch($value, &$jumps = 0)
    {
        if (count($this->list) === 0) {
            return null;
        }
        if (!$this->sorted) {
            $this->sort();
        }
        $start = 0;
        $end = count($this->list) - 1;

        while ($start <= $end) {
            $jumps++;
            $mid = intval(($start + $end) / 2);
            $chk = $this->list[$mid];
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

    /**
     * @param SortedIndex[] $indexes
     */
    public static function intersect($indexes)
    {
        usort($indexes, function ($a, $b) {
            return count($a) - count($b);
        });
        $first = array_shift($indexes);
        $base = $first->list;
        foreach ($indexes as $index) {
            $newBase = [];
            foreach ($base as $val) {
                if ($index->contains($val)) {
                    $newBase[] = $val;
                }
            }
            $base = $newBase;
        }
        return array_values($base);
    }

    /**
     * @return static
     */
    public static function newEmpty()
    {
        static $empty;
        if (!isset($empty)) {
            $empty = new static([]);
        }
        return clone $empty;
    }
}
