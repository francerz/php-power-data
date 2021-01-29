<?php
namespace Francerz\PowerData;

use LogicException;
use Traversable;

class Arrays
{
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

    static public function filter($array, ?callable $callback = null, int $flag = 0)
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
    static public function nest(array $array1, array $array2, string $name, callable $compare)
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
            } elseif (is_array($v2)) {
                $v1[$name] = $matches;
            }
        }
        return $array1;
    }
}