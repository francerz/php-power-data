<?php

namespace Francerz\PowerData;

use LogicException;
use Traversable;

class Arrays
{
    public const NEST_COLLECTION = 0;
    public const NEST_SINGLE_FIRST = 1;
    public const NEST_SINGLE_LAST = 2;

    /**
     * Checks whether array has numeric keys.
     *
     * @param array $array Array to check.
     * @return boolean
     */
    public static function hasNumericKeys(array $array)
    {
        return count(array_filter($array, 'is_numeric', ARRAY_FILTER_USE_KEY)) > 0;
    }

    /**
     * Checks whether array has string keys.
     *
     * @param array $array Array to check.
     * @return boolean
     */
    public static function hasStringKeys(array $array)
    {
        return count(array_filter($array, 'is_string', ARRAY_FILTER_USE_KEY)) > 0;
    }

    /**
     * Find all keys that matches with given pattern.
     *
     * @param array $array Array to check.
     * @param string $pattern Pattern to filter keys.
     * @return array
     */
    public static function findKeys(array $array, string $pattern)
    {
        return array_filter($array, function ($k) use ($pattern) {
            return preg_match($pattern, $k);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Removes array items when matching given value.
     *
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public static function remove(array &$array, $value)
    {
        $array = array_filter($array, function ($v) use ($value) {
            return ($v !== $value);
        });
    }

    /**
     * Iterates over each value in the array passing them to the callback function.
     *
     * If the callback function returns true, the current value from array is
     * returned into the result array. Array keys are preserved.
     *
     * @param array $array
     * @param callable|null $callback
     * @param integer $flag
     * @return void
     */
    public static function filter($array, $callback = null, $flag = 0)
    {
        if (is_array($array)) {
            return array_filter($array, $callback, $flag);
        }
        if (!$array instanceof Traversable) {
            throw new LogicException('Invalid array for filter, must be array or Traversable');
        }
        $new = [];
        if (is_null($callback)) {
            foreach ($array as $k => $v) {
                if ($v) {
                    $new[$k] = $v;
                }
            }
            return $new;
        }
        switch ($flag) {
            case ARRAY_FILTER_USE_KEY:
                foreach ($array as $k => $v) {
                    if ($callback($k)) {
                        $new[$k] = $v;
                    }
                }
                return $new;
            case ARRAY_FILTER_USE_BOTH:
                foreach ($array as $k => $v) {
                    if ($callback($v, $k)) {
                        $new[$k] = $v;
                    }
                }
                return $new;
        }
        foreach ($array as $k => $v) {
            if ($callback($v)) {
                $new[$k] = $v;
            }
        }
        return $new;
    }

    public static function intersect(array $array1, array $array2, ...$_)
    {
        $args = func_get_args();
        $args[] = function ($a, $b) {
            $ak = is_object($a) ? spl_object_hash($a) : $a;
            $bk = is_object($b) ? spl_object_hash($b) : $b;
            return strcmp($ak, $bk);
        };
        return call_user_func_array('array_uintersect', $args);
    }

    public static function keyInsensitive(array $array, string $key)
    {
        if (array_key_exists($key, $array)) {
            return $key;
        }

        $ikey = strtolower($key);
        foreach ($array as $k => $v) {
            if (strtolower($k) == $ikey) {
                return $k;
            }
        }
        return null;
    }

    public static function valueKeyInsensitive(array $array, string $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $ikey = strtolower($key);
        foreach ($array as $k => $v) {
            if (strtolower($k) == $ikey) {
                return $v;
            }
        }
        return null;
    }

    /**
     * Combines two arrays by putting matching $children as attribute in each
     * $parent item.
     *
     * @param array $parent Parent array, which each children MUST be object or array.
     * @param array $children Children array, these will bi imported for every parent item.
     * @param string $name Attribute name for matching children in each parent item.
     * @param callable $compare Callback function to compare and find matches in parents and children.
     * @param int $mode Nesting mode for children, which can be
     *      COLLECTION: an array of matching children.
     *      SINGLE_FIRST: first found matching children.
     *      SINGLE_LAST: last found matching children.
     * @return void
     */
    public static function nest(
        array $parent,
        array $children,
        string $name,
        callable $compare,
        $mode = self::NEST_COLLECTION
    ) {
        switch ($mode) {
            case self::NEST_COLLECTION:
            default:
                return static::nestCollection($parent, $children, $name, $compare);
            case self::NEST_SINGLE_FIRST:
                return static::nestSingleFirst($parent, $children, $name, $compare);
            case self::NEST_SINGLE_LAST:
                $array2 = array_reverse($children, true);
                return static::nestSingleFirst($parent, $array2, $name, $compare);
        }
        return null;
    }

    private static function nestCollection(array $array1, array $array2, string $name, callable $compare)
    {
        foreach ($array1 as &$v1) {
            $matches = [];
            foreach ($array2 as &$v2) {
                if ($compare($v1, $v2)) {
                    $matches[] = $v2;
                }
            }
            if (is_object($v1)) {
                $v1->$name = $matches;
            } elseif (is_array($v1)) {
                $v1[$name] = $matches;
            }
        }
        return $array1;
    }

    private static function nestSingleFirst(array $array1, array $array2, string $name, callable $compare)
    {
        foreach ($array1 as &$v1) {
            $match = null;
            foreach ($array2 as &$v2) {
                if ($compare($v1, $v2)) {
                    $match = $v2;
                    break;
                }
            }
            if (is_object($v1)) {
                $v1->$name = $match;
            } elseif (is_array($v1)) {
                $v1[$name] = $match;
            }
        }
        return $array1;
    }

    public static function index(array $values)
    {
        $index = [];
        foreach ($values as $k => $v) {
            if (!isset($index[$v])) {
                $index[$v] = [];
            }
            $index[$v][] = $k;
        }
        return $index;
    }

    /**
     * @param array $array
     * @param array $keys
     * @param array|null $newKeys
     * @return void
     */
    public static function replaceKeys(array $array, array $keys, $newKeys = null)
    {
        if (isset($newKeys) && count($keys) != count($newKeys)) {
            throw new LogicException('Params $keys and $newKeys must have same length.');
        }

        $keys = isset($newKeys) ? array_combine($keys, $newKeys) : $keys;

        $new = [];
        foreach ($keys as $k => $n) {
            $new[$n] = array_key_exists($k, $array) ? $array[$k] : null;
        }
        return $new;
    }

    /**
     * Creates an array from given iterable.
     *
     * @param iterable $iterable
     * @param boolean $keepKeys
     * @return array
     */
    public static function fromIterable($iterable, $keepKeys = true)
    {
        if (is_array($iterable)) {
            return $iterable;
        }

        $array = [];
        if ($keepKeys) {
            foreach ($iterable as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }

        foreach ($iterable as $value) {
            $array[] = $value;
        }
        return $array;
    }
}
