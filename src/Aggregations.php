<?php

namespace Francerz\PowerData;

class Aggregations
{
    public static function count(array $values)
    {
        return count($values);
    }

    public static function sum(array $values)
    {
        $sum = 0;
        foreach ($values as $v) {
            $sum += $v;
        }
        return $sum;
    }

    public static function product(array $values)
    {
        if (empty($values)) {
            return null;
        }
        $product = array_shift($values);
        foreach ($values as $v) {
            $product *= $v;
        }
        return $product;
    }

    public static function min(array $values)
    {
        if (empty($values)) {
            return null;
        }
        $min = array_shift($values);
        foreach ($values as $v) {
            if ($min > $v) {
                $min = $v;
            }
        }
        return $min;
    }

    public static function max(array $values)
    {
        if (empty($values)) {
            return null;
        }
        $max = array_shift($values);
        foreach ($values as $v) {
            if ($max < $v) {
                $max = $v;
            }
        }
        return $max;
    }

    public static function first(array $values)
    {
        return reset($values) ?: null;
    }

    public static function last(array $values)
    {
        return end($values) ?: null;
    }

    public static function concat(array $values, $separator = '')
    {
        if (empty($values)) {
            return '';
        }
        $concat = array_shift($values);
        foreach ($values as $v) {
            $concat .= $separator . (string)$v;
        }
        return $concat;
    }

    public static function mode(array $values, bool $strict = false)
    {
        if (empty($values)) {
            return [];
        }
        $freq = [];
        foreach ($values as $v) {
            if (!isset($freq[$v])) {
                $freq[$v] = 1;
                continue;
            }
            $freq[$v] += 1;
        }
        $modes = array_keys($freq, max($freq), $strict);
        return count($modes) == count($freq) ? [] : $modes;
    }

    public static function mean(array $values)
    {
        return static::sum($values) / (static::count($values) * 1.0);
    }

    public static function percentile(array $values, float $percentile)
    {
        $percentile = min(max(0, $percentile), 100);
        sort($values);
        $values = array_values($values);
        $index = $percentile / 100 * (count($values) - 1);
        $indexFloor = floor($index);
        $indexFraction = $index - $indexFloor;

        $val = $values[$index];
        if ($indexFraction > 0) {
            $val += $indexFraction * ($values[$index + 1] - $values[$index]);
        }
        return $val;
    }

    public static function median(array $values)
    {
        return static::percentile($values, 50);
    }
}
