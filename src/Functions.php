<?php

namespace Francerz\PowerData;

use ReflectionFunction;

class Functions
{
    public static function testSignature(callable $function, array $args = [], ?string $retType = 'void', ?string $name = null) : bool
    {
        $rf = new ReflectionFunction($function);
        $params = $rf->getParameters();
        if (count($params) != count($args)) {
            return false;
        }
        foreach ($args as $i => $argType) {
            if ($params[$i]->getType()->getName() != $argType) {
                return false;
            }
        }
        $rt = $rf->getReturnType();
        if ($retType !== 'void') {
            if ($rt == null) {
                return false;
            }
            if ($rt->getName() != $retType) {
                return false;
            }
        } elseif ($rt != null) {
            return false;
        }
        if (isset($name) && $rf->getName() !== $name) {
            return false;
        }
        return true;
    }
}