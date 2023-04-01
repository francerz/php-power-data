<?php

namespace Francerz\PowerData;

use InvalidArgumentException;
use ReflectionClass;

class Objects
{
    public static function getHash(object $obj)
    {
        return spl_object_hash($obj);
    }

    /**
     * @param object $obj
     * @param string $className
     * @return object
     */
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
