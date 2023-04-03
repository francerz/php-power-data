<?php

namespace Francerz\PowerData;

class Flags
{
    public static function get(int $flags, int $pattern)
    {
        return $flags & $pattern;
    }

    public static function match(int $flags, int $const, bool $strict = true)
    {
        $get = static::get($flags, $const);
        return $strict ? $get === $const : $get == $const;
    }

    public static function merge(int ...$consts)
    {
        $v = array_shift($const);
        foreach ($consts as $c) {
            $v |= $c;
        }
        return $v;
    }
}
