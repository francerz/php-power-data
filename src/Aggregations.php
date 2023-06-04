<?php

namespace Francerz\PowerData;

class Aggregations
{
    /**
     * Counts items in array.
     *
     * @param array $values
     * @param bool $ignoreNulls
     * @return int
     */
    public static function count(array $values, bool $ignoreNulls = false)
    {
        if (!$ignoreNulls) {
            return count($values);
        }
        $count = 0;
        foreach ($values as $v) {
            if (isset($v)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Sums every item in array.
     *
     * @param array $values
     * @return int|float
     */
    public static function sum(array $values)
    {
        return array_sum($values);
    }

    /**
     * Multiplicates every value in array.
     *
     * @param array $values
     * @param boolean $ignoreEmpty
     * @return int|float
     */
    public static function product(array $values, bool $ignoreEmpty = false)
    {
        if (empty($values)) {
            return null;
        }
        if ($ignoreEmpty) {
            return array_reduce($values, function ($c, $v) {
                if (empty($v)) {
                    return $c;
                }
                return $c * $v;
            }, 1);
        }
        return array_reduce($values, function ($c, $v) {
            return $c * $v;
        }, 1);
    }

    /**
     * Retrieves minimum value in array.
     *
     * @param array $values
     * @return int|float
     */
    public static function min(array $values)
    {
        if (empty($values)) {
            return null;
        }
        return min($values);
    }

    /**
     * Retrieves maximum value in array.
     *
     * @param array $values
     * @return int|float
     */
    public static function max(array $values)
    {
        if (empty($values)) {
            return null;
        }
        return max($values);
    }

    /**
     * Retrieves first value in array.
     *
     * @param array $values
     * @return void
     */
    public static function first(array $values)
    {
        return reset($values) ?: null;
    }

    /**
     * Retrieves last value in array.
     *
     * @param array $values
     * @return mixed
     */
    public static function last(array $values)
    {
        return end($values) ?: null;
    }

    /**
     * Concatenates every value in array.
     *
     * Alias of implode with inverted argruments.
     *
     * @param array $values
     * @param string $separator
     * @return string
     */
    public static function concat(array $values, $separator = '')
    {
        return implode($separator, $values);
    }

    /**
     * Counts all values in array.
     *
     * Alias of array_count_values.
     *
     * @param array $values
     * @return array
     */
    public static function frequencies(array $values)
    {
        return array_count_values($values);
    }

    /**
     * Filter the most frequents values in array.
     *
     * @param array $values
     * @param boolean $strict
     * @return array
     */
    public static function mode(array $values, bool $strict = false)
    {
        if (empty($values)) {
            return [];
        }
        $freq = self::frequencies($values);
        $modes = array_keys($freq, max($freq), $strict);
        return count($modes) == count($freq) ? [] : $modes;
    }

    /**
     * Calculates the mean in given values array.
     *
     * @param array $values
     * Array of values.
     *
     * @param bool $ignoreNulls
     * Discards null values from calculation.
     *
     * @return float
     */
    public static function mean(array $values, bool $ignoreNulls = false)
    {
        return static::sum($values) / (static::count($values, $ignoreNulls) * 1.0);
    }

    /**
     * Retrieves median value in array.
     *
     * This is the same as retrieving 50th percentile.
     *
     * @param array $values
     * Array of values.
     *
     * @return float
     */
    public static function median(array $values)
    {
        return static::percentile($values, 50);
    }

    /**
     * Calculates percentile from first value appearance.
     */
    public const PERCENTILE_FLAGS_FIRST  = 0b0001;
    /**
     * Calculates percentile from the middle appearance.
     */
    public const PERCENTILE_FLAGS_MIDDLE   = 0b0010;
    /**
     * Calculates percentile from last value appearance.
     */
    public const PERCENTILE_FLAGS_LAST   = 0b0100;
    /**
     * Remove duplicated values.
     */
    public const PERCENTILE_FLAGS_UNIQUE = 0b1000;

    /**
     * Retrieves value at given percentile from a values collection.
     *
     * This function calculates fractional part from percentile if this doesn't match with any specific value.
     *
     * @param flaot[]|int[] $values
     * Array with values to find.
     *
     * @param float $percentile
     * Percentile from 0 to 100.
     *
     * @param int $flags
     * Percentile flags. Only works with PERCENTILE_FLAGS_UNIQUE.
     *
     * @return float
     */
    public static function percentile(array $values, float $percentile, int $flags = self::PERCENTILE_FLAGS_MIDDLE)
    {
        $percentile = min(max(0, $percentile), 100);
        if (Flags::match($flags, self::PERCENTILE_FLAGS_UNIQUE)) {
            $values = array_unique($values, SORT_ASC);
        }
        sort($values);
        $values = array_values($values);
        $index = $percentile / 100 * (count($values) - 1);
        $indexFloor = floor($index);
        $indexFraction = $index - $indexFloor;

        $val = $values[$indexFloor];
        if ($indexFraction > 0) {
            $val += $indexFraction * ($values[$indexFloor + 1] - $values[$indexFloor]);
        }
        return $val;
    }

    /**
     * Calculates percentile position for given value.
     *
     * Percentile range goes from 0 to 100.
     *
     * If $values array is empty, this function will return null.
     *
     * If $value is lower than minimum then will return 0. If $value is higher to maximum then will return 100.
     *
     * @param array $values
     * Array of sample values.
     *
     * @param float $value
     * Value to lookup.
     *
     * @param int $flags
     * Determines the algorithm behavior, like removing duplicates and calculating from first or last appearance.
     *
     * @return float|null
     */
    public static function findPercentile(array $values, float $value, int $flags = self::PERCENTILE_FLAGS_MIDDLE)
    {
        if (empty($values)) {
            return null;
        }
        if (Flags::match($flags, self::PERCENTILE_FLAGS_UNIQUE)) {
            $values = array_unique($values);
        }
        sort($values);
        $values = array_values($values);
        $firstI = $lastI = $nextI = 0;
        $prevV = null;
        foreach ($values as $k => $v) {
            if ($v == $prevV) {
                $lastI = $nextI = $k;
                continue;
            }
            if ($v > $value) {
                $nextI = $k;
                break;
            }
            $firstI = $lastI = $k;
            $prevV = $v;
        }
        $countLess1 = count($values) - 1;

        $diff = $values[$nextI] - $values[$lastI];
        $excess = $value - $values[$lastI];
        $pct = 0;
        if ($excess > 0) {
            $pct = $lastI / $countLess1;
        } elseif (Flags::match($flags, self::PERCENTILE_FLAGS_MIDDLE)) {
            $pct = ($firstI + $lastI) / 2 / $countLess1;
        } elseif (Flags::match($flags, self::PERCENTILE_FLAGS_FIRST)) {
            $pct = $firstI / $countLess1;
        } elseif (Flags::match($flags, self::PERCENTILE_FLAGS_LAST)) {
            $pct = $lastI / $countLess1;
        }
        if ($excess > 0) {
            $pct += $diff > 0 ? $excess / ($diff * $countLess1) : 0;
        }
        return $pct * 100;
    }
}
