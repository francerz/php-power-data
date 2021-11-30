<?php
namespace Francerz\PowerData;

use LogicException;
use Traversable;

class Arrays
{
    const NEST_COLLECTION = 0;
    const NEST_SINGLE_FIRST = 1;
    const NEST_SINGLE_LAST = 2;
    static public function hasNumericKeys(array $array)
    {
        return count(array_filter($array, 'is_numeric', ARRAY_FILTER_USE_KEY)) > 0;
    }
    static public function hasStringKeys(array $array)
    {
        return count(array_filter($array, 'is_string', ARRAY_FILTER_USE_KEY)) > 0;
    }
    static public function findKeys(array $array, string $pattern)
    {
        return array_filter($array, function($k) use ($pattern) {
            return preg_match($pattern, $k);
        }, ARRAY_FILTER_USE_KEY);
    }
    static public function remove(array &$array, $value)
    {
        $array = array_filter($array, function($v) use ($value) {
            return ($v !== $value);
        });
    }

    /**
     * Undocumented function
     *
     * @param array $array
     * @param callable|null $callback
     * @param integer $flag
     * @return void
     */
    static public function filter($array, $callback = null, $flag = 0)
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
                if ($v) $new[$k] = $v;
            }
            return $new;
        }
        switch ($flag) {
            case ARRAY_FILTER_USE_KEY:
                foreach ($array as $k => $v) {
                    if ($callback($k)) $new[$k] = $v;
                }
                return $new;
            case ARRAY_FILTER_USE_BOTH:
                foreach ($array as $k => $v) {
                    if ($callback($v, $k)) $new[$k] = $v;
                }
                return $new;
        }
        foreach ($array as $k => $v) {
            if ($callback($v)) $new[$k] = $v;
        }
        return $new;
    }
    static public function intersect(array $array1, array $array2, ...$_)
    {
        $args = func_get_args();
        $args[] = function($a, $b) {
            $ak = is_object($a) ? spl_object_hash($a) : $a;
            $bk = is_object($b) ? spl_object_hash($b) : $b;
            return strcmp($ak, $bk);
        };
        return call_user_func_array('array_uintersect', $args);
    }
    static public function keyInsensitive(array $array, string $key)
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
    static public function valueKeyInsensitive(array $array, string $key)
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
    public static function nest(array $array1, array $array2, string $name, callable $compare, $mode = self::NEST_COLLECTION)
    {
        switch ($mode) {
            case self::NEST_COLLECTION: default:
                return static::nestCollection($array1, $array2, $name, $compare);
            case self::NEST_SINGLE_FIRST:
                return static::nestSingleFirst($array1, $array2, $name, $compare);
            case self::NEST_SINGLE_LAST:
                $array2 = array_reverse($array2, true);
                return static::nestSingleFirst($array1, $array2, $name, $compare);
        }
        return null;
    }
    private static function nestCollection(array $array1, array $array2, string $name, callable $compare)
    {
        foreach($array1 as &$v1) {
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
     * @param iterable $iterable
     * @param boolean $keepKeys
     * @return void
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
