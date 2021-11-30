<?php

namespace Francerz\PowerData;

use ReflectionFunction;

class Functions
{
    /**
     * Undocumented function
     *
     * @param callable $function
     * @param array $args
     * @param string|null $retType
     * @param string|null $name
     * @return boolean
     */
    public static function testSignature(callable $function, array $args = [], $retType = 'void', $name = null)
    {
        $rf = new ReflectionFunction($function);
        $params = $rf->getParameters();
        if (count($params) != count($args)) {
            return false;
        }
        foreach ($args as $i => $argType) {
            if (!method_exists($params[$i], 'getType')) {
                break;
            }
            $paramType = $params[$i]->getType()->getName();
            if ($paramType === $argType) {
                continue;
            }
            if (class_exists($paramType) && is_subclass_of($paramType, $argType)) {
                continue;
            }
            return false;
        }
        if (!method_exists($rf, 'getReturnType')) {
            return true;
        }
        $rt = $rf->getReturnType();
        if ($retType !== 'void') {
            if ($rt == null) {
                return false;
            }
            $rtName = $rt->getName();
            if ($rtName !== $retType) {
                $return = false;
                if (class_exists($rtName) && is_subclass_of($rtName, $retType)) {
                    $return = true;
                }
                if (!$return) {
                    return false;
                }
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
