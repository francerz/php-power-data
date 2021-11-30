<?php
namespace Francerz\PowerData;

use Exception;

class Tree
{
    /** @var TreeNode|null */
    private $root;

    public function __construct()
    {
        $this->index = new Map();
    }
    private function setRoot(TreeNode $root)
    {
        $this->root = $root;
    }
    public function getRoot()
    {
        return $this->root;
    }
    protected static function getValue($row, $column)
    {
        if (is_array($row) && isset($row[$column])) {
            return $row[$column];
        } elseif (is_object($row)) {
            return $row->{$column};
        }
        return null;
    }

    public static function fromArray(array $array, string $itemKey, string $parentKey)
    {
        $tree = new Tree();
        $index = new Map();

        foreach ($array as $v) {
            $key = static::getValue($v, $itemKey);
            $index->add($key, new TreeNode($tree, $v));
        }

        $root = array();
        foreach ($index as $node) {
            $pk = static::getValue($node->getValue(), $parentKey);
            if (is_null($pk)) {
                $root[] = $node;
                continue;
            }
            $p = $index->get($pk);
            $node->setParent($p);
        }

        if (count($root) == 0) {
            throw new PowerDataException('Tree::fromArray(): Not root item found.');
        } elseif (count($root) > 1) {
            throw new PowerDataException('Tree::fromArray(): Multiple root items found.');
        }

        $tree->setRoot($root[0]);

        return $tree;
    }

    /**
     * @param callable $keyFunction
     * @return Map
     */
    public function getMap($keyFunction)
    {
        $map = new Map();
        $this->recursiveMap($map, $this->root, $keyFunction);
        return $map;
    }

    /**
     * @param Map $map
     * @param TreeNode $node
     * @param callable $keyFunction
     * @return void
     */
    private function recursiveMap(Map $map, TreeNode $node, $keyFunction)
    {
        $key = $keyFunction($node);
        $map->add($key, $node);
        foreach ($node->getChildren() as $child) {
            $this->recursiveMap($map, $child, $keyFunction);
        }
    }
}
