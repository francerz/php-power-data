<?php

namespace Francerz\PowerData;

use InvalidArgumentException;

class Objects
{
    static public function getHash(object $obj) : string
    {
        return spl_object_hash($obj);
    }

    public static function cast(object $obj, string $className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Inexistent class %s', $className));
        }
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(strstr(serialize($obj), '"'), ':')
        ));
    }
}