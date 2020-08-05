<?php
namespace Francerz\PowerData;

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
    static public function remove(array &$array, $value)
    {
        $array = array_filter($array, function($v) use ($value) {
            return ($v !== $value);
        });
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
        $ikey = strtolower($key);
        foreach ($array as $k => $v) {
            if (strtolower($k) == $ikey) {
                return $v;
            }
        }
        return null;
    }
}