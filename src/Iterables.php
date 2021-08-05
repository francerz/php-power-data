<?php

namespace Francerz\PowerData;

use LogicException;

abstract class Iterables
{
    private static function filterItemColumns($item, array $columns, ?array $replace = null)
    {
        if (isset($replace) && count($replace) != count($columns)) {
            throw new LogicException('$replace and $columns MUST have same length.');
        }
        if (is_object($item)) {
            $item = (array)$item;
        }
        if (!is_array($item)) {
            return $item;
        }
        if (is_null($replace)) {
            return array_intersect_key($item, array_flip($columns));
        }
        $replace = array_values($replace);
        $new = []; $i = 0;
        foreach ($columns as $cv) {
            $new[$replace[$i++]] = $item[$cv];
        }
        return $new;
    }

    public static function filterColumns(iterable $iterable, array $columns, ?array $replace = null)
    {
        $result = [];
        foreach ($iterable as $item) {
            $result[] = static::filterItemColumns($item, $columns, $replace);
        }
        return $result;
    }

    public static function diff(iterable $a, iterable $b, ?callable $comparer = null, ?callable $asort = null, ?callable $bsort = null)
    {
        $a = Arrays::fromIterable($a);
        $b = Arrays::fromIterable($b);

        if (!isset($comparer)) {
            return array_diff($a, $b);
        }

        isset($asort) ? uasort($a, $asort) : asort($a);
        isset($bsort) ? uasort($b, $bsort) : asort($b);
        return array_udiff($a, $b, $comparer);
    }

    const NEST_COLLECTION = 0;
    const NEST_SINGLE_FIRST = 1;
    const NEST_SINGLE_LAST = 2;
    public static function nest(
        iterable $parents,
        iterable $children,
        string $name,
        callable $compare,
        $mode = self::NEST_COLLECTION
    ) {
        switch ($mode) {
            case self::NEST_SINGLE_FIRST:
                return static::nestSingleFirst($parents, $children, $name, $compare);
            case self::NEST_SINGLE_LAST:
                $children = array_reverse(Arrays::fromIterable($children));
                return static::nestSingleFirst($parents, $children, $name, $compare);
            case self::NEST_COLLECTION: default:
                return static::nestCollection($parents, $children, $name, $compare);
        }
    }

    private static function nestCollection($parents, $children, $name, $compare)
    {
        $newParents = [];
        foreach ($parents as $k => $parent) {
            $matches = [];
            foreach ($children as $k2 => $child) {
                if ($compare($parent, $child)) {
                    $matches[$k2] = $child;
                }
            }
            if (is_object($parent)) {
                $parent->$name = $matches;
            } elseif (is_array($parent)) {
                $parent[$name] = $matches;
            }
            $newParents[$k] = $parent;
        }
        return $newParents;
    }

    private static function nestSingleFirst($parents, $children, $name, $compare)
    {
        $newParents = [];
        foreach ($parents as $k => $parent) {
            $match = null;
            foreach ($children as $child) {
                if ($compare($parent, $child)) {
                    $match = $child;
                    break;
                }
            }
            if (is_object($parent)) {
                $parent->$name = $match;
            } elseif (is_array($parent)) {
                $parent[$name] = $match;
            }
            $newParents[$k] = $parent;
        }
        return $newParents;
    }
}