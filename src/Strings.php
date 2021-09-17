<?php

namespace Francerz\PowerData;

abstract class Strings
{
    public static function endsWith(string $haystack, string $needle)
    {
        $l = strlen($needle);
        return $l > 0 ? substr($haystack, -$l) === $needle : true;
    }
}