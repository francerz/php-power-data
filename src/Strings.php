<?php

namespace Francerz\PowerData;

abstract class Strings
{
    public static function endsWith(string $haystack, string $needle)
    {
        $l = strlen($needle);
        return $l > 0 ? substr($haystack, -$l) === $needle : true;
    }

    public static function startsWith(string $haystack, string $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    public static function contains(string $haystack, string $needle)
    {
        return strpos($haystack, $needle) !== false;
    }
}
