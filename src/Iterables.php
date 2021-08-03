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
}