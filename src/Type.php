<?php

namespace Francerz\PowerData;

use Exception;

/**
 * @deprecated v0.1.26
 */
class Type
{
    #region Static Init
    /** @var bool */
    private static $init = false;

    /** @var array */
    private static $primitives;

    /** @var array */
    private static $knownTypesNames;

    /** @var Tree */
    private static $knownTypesTree;

    /** @var array */
    private static $knownTypes = array(
        array(
            'type'      => 'bool',
            'primitive' => true,
            'checkFunc' => 'is_bool',
            'parent'    => 'scalar'
        ),
        array(
            'type'      => 'boolean',
            'remap'     => 'bool'
        ),
        array(
            'type'      => 'int',
            'primitive' => true,
            'checkFunc' => 'is_int',
            'parent'    => 'number'
        ),
        array(
            'type'      => 'integer',
            'remap'     => 'int'
        ),
        array(
            'type'      => 'float',
            'primitive' => true,
            'checkFunc' => 'is_float',
            'parent'    => 'number'
        ),
        array(
            'type'      => 'double',
            'remap'     => 'float'
        ),
        array(
            'type'      => 'number',
            'primitive' => true,
            'checkFunc' => 'is_numeric',
            'parent'    => 'scalar'
        ),
        array(
            'type'      => 'string',
            'primitive' => true,
            'checkFunc' => 'is_string',
            'parent'    => 'scalar'
        ),
        array(
            'type'      => 'array',
            'primitive' => false,
            'checkFunc' => 'is_array',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'object',
            'primitive' => false,
            'checkFunc' => 'is_object',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'resource',
            'primitive' => false,
            'checkFunc' => 'is_resource',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'scalar',
            'primitive' => true,
            'checkFunc' => 'is_scalar',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'null',
            'primitive' => false,
            'checkFunc' => 'is_null',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'callable',
            'primitive' => false,
            'checkFunc' => 'is_callable',
            'parent'    => 'mixed'
        ),
        array(
            'type'      => 'mixed',
            'primitive' => false,
            'checkFunc' => 'isset'
        )
    );

    private static function staticInit()
    {
        if (static::$init) {
            return;
        }

        static::$knownTypes = array_column(static::$knownTypes, null, 'type');

        static::$primitives = array_column(array_filter(
            static::$knownTypes,
            function ($v) {
                return !empty($v['primitive']);
            }
        ), 'type');

        static::$knownTypesNames = array_column(static::$knownTypes, 'type');

        static::$knownTypesTree = Tree::fromArray(
            array_filter(
                static::$knownTypes,
                function ($v) {
                    return !isset($v['remap']);
                }
            ),
            'type',
            'parent'
        );

        static::$init = true;
    }
    /**
     * @param string $type
     * @return array|null
     */
    private static function getKnownType(string $type)
    {
        if (isset(static::$knownTypes[$type])) {
            if (isset(static::$knownTypes[$type]['remap'])) {
                return static::getKnownType(static::$knownTypes[$type]['remap']);
            }
            return static::getKnownType(static::$knownTypes[$type]);
        }
        return null;
    }
    /**
     * @param string $type
     * @return string|null
     */
    private static function getKeyKnownType(string $type)
    {
        if (isset(static::$knownTypes[$type]) && isset(static::$knownTypes[$type]['remap'])) {
            return static::getKeyKnownType(static::$knownTypes[$type]['remap']);
        }
        return $type;
    }
    #endregion

    private static $typeCache = [];

    /**
     * @param string $type
     * @param integer|null $depth
     * @param integer|null $maxDepth
     * @return Type
     */
    final public static function forKey($type, $depth = null, $maxDepth = null)
    {
        // initialize class static properties.
        static::staticInit();

        // checks type string, base type and array depth.
        if (!preg_match(Type::TYPE_FORMAT_PATTERN, $type, $matches)) {
            throw new Exception("Type(): Invalid type string '{$type}'");
        }

        // catches base type and array depth.
        $type = static::getKeyKnownType($matches[1]);
        $depth = isset($depth) ? $depth : substr_count($matches[2], '[]');

        // defines a key for caching
        $typeKey = "$type:$depth-$maxDepth";

        // checks cache and returns if found.
        if (array_key_exists($typeKey, static::$typeCache)) {
            return static::$typeCache[$typeKey];
        }

        // creates not cached type
        return static::$typeCache[$typeKey] = new static($type, $depth, $maxDepth);
    }

    /**
     * @param mixed $value
     * @return Type
     */
    final public static function of($value)
    {
        if (is_array($value)) {
            return static::ofArray($value);
        }
        $type = gettype($value);
        if ($type == 'object') {
            $type = get_class($value);
        }
        return Type::forKey($type);
    }
    private static function ofArray(array $values)
    {
        $types = [];
        foreach ($values as $val) {
            $types[] = static::of($val);
        }
        $type = static::getCommonTypeArray($types);
        return static::forKey($type->type, $type->arrayDepth + 1, $type->arrayMaxDepth);
    }

    /**
     * @param array $types
     * @return Type
     */
    private static function getCommonTypeArray(array $types)
    {
        if (count($types) == 0) {
            return static::forKey('mixed');
        }
        $types = array_unique($types);
        if (count($types) == 1) {
            return $types[0];
        }
        $typeGroups = static::groupTypes($types);
        if (count($typeGroups) == 1) {
            reset($typeGroups);
            $type = key($typeGroups);
            return static::forKey($type, 1);
        }
        $type = static::getCommonType(array_keys($typeGroups));
        return static::forKey($type);
    }

    /**
     * @param array $type_keys
     * @return string
     */
    private static function getCommonType(array $type_keys)
    {
        $map = static::$knownTypesTree->getMap(function ($node) {
            return $node->getValue()['type'];
        });
        $paths = [];

        foreach ($type_keys as $k) {
            $n = $map->get($k);
            if (is_null($n)) {
                $n = $map->get('object');
            }
            $paths[] = $n->getPathToRoot();
        }

        $inters = call_user_func_array([Arrays::class,'intersect'], $paths);
        if (count($inters) == 0) {
            return 'mixed';
        } elseif (count($inters) == 1) {
            return current($inters)->getValue()['type'];
        } else {
            return current($inters)->getValue()['type'];
        }
        return "mixed";
    }

    /**
     * @param array $types
     * @return array
     */
    private static function groupTypes(array $types)
    {
        $typeMap = [];
        foreach ($types as $type) {
            $key = $type->getType();
            $depth = $type->getMaxDepth();
            if (!array_key_exists($key, $typeMap)) {
                $typeMap[$key] = [];
            }
            if (!array_key_exists($depth, $typeMap[$key])) {
                $typeMap[$key][$depth] = 0;
            }
            $typeMap[$key][$depth] += 1;
        }
        return $typeMap;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return boolean
     */
    final public static function is($value, string $type)
    {
        $type = static::forKey($type);
        return $type->check($value);
    }

    public const TYPE_FORMAT_PATTERN = '/^(?:\\\)?([\\\A-Za-z0-9_]+)((?:\[\])*)$/';

    private $type;
    private $arrayDepth = 0;
    private $arrayMaxDepth = null;

    /**
     * @param string $type
     * @param integer $depth
     * @param integer|null $maxDepth
     */
    final private function __construct($type, $depth = 0, $maxDepth = null)
    {
        $this->type = $type;
        $this->arrayDepth = $depth;
        $this->arrayMaxDepth = $maxDepth;
    }

    public function isKnownType()
    {
        return in_array($this->type, static::$knownTypesNames);
    }

    public function isPrimitive()
    {
        return in_array($this->type, static::$primitives);
    }

    public function isClass()
    {
        if ($this->isKnownType()) {
            return false;
        }
        return true;
    }

    public function isArray()
    {
        return $this->arrayDepth > 0;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getClassName()
    {
        if (!$this->isClass()) {
            return null;
        }
        return substr($this->type, strrpos($this->type, '\\') + 1);
    }

    public function getNamespace()
    {
        if (!$this->isClass()) {
            return null;
        }
        return substr($this->type, 0, strrpos($this->type, '\\'));
    }

    public function getArrayDepth()
    {
        return $this->arrayDepth;
    }
    public function getMaxDepth()
    {
        if (isset($this->arrayMaxDepth)) {
            return $this->arrayMaxDepth;
        }
        return $this->arrayDepth;
    }

    /**
     * @return callable
     */
    private function getTypeCheckFunction()
    {
        if ($this->isKnownType()) {
            return static::$knownTypes[$this->type]['checkFunc'];
        }
        return (function ($value) {
            return $value instanceof $this->type;
        });
    }

    /**
     * @param callable $check
     * @param mixed $value
     * @param integer $depth
     * @return void
     */
    private function checkArrayRecursive($check, $value, $depth)
    {
        if ($depth == 0) {
            return $check($value);
        }
        if (!is_iterable($value)) {
            return false;
        }
        foreach ($value as $v) {
            if (!$this->checkArrayRecursive($check, $v, $depth - 1)) {
                return false;
            }
        }
        return true;
    }

    public function check($value)
    {
        $checkFunc = $this->getTypeCheckFunction();
        if (!$this->isArray()) {
            return $checkFunc($value);
        }
        return $this->checkArrayRecursive($checkFunc, $value, $this->arrayDepth);
    }

    public function __toString()
    {
        if (isset($this->arrayMaxDepth)) {
            return $this->type . "[{$this->arrayDepth}-{$this->arrayMaxDepth}]";
        }
        return $this->type . str_repeat('[]', $this->arrayDepth);
    }
}
