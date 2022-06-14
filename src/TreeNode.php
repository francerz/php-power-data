<?php

namespace Francerz\PowerData;

use Exception;

/**
 * @deprecated v0.1.26
 */
class TreeNode
{
    private $tree;
    private $parent = null;
    private $value;
    private $children = array();

    public function __construct(Tree $tree, $value)
    {
        $this->value = $value;
        $this->tree = $tree;
    }
    public function setParent(TreeNode $newParent)
    {
        if ($this->parent === $newParent) {
            return;
        }

        if ($newParent->tree !== $this->tree) {
            throw new Exception('TreeNode->setParent(): incompatible nodes, different tree');
        }

        $this->unsetParent();

        $this->parent = $newParent;
        $this->parent->children[] = $this;
    }
    public function unsetParent()
    {
        if (isset($this->parent)) {
            Arrays::remove($this->parent->children, $this);
        }
    }
    public function getTree()
    {
        return $this->tree;
    }
    public function getParent()
    {
        return $this->parent;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getChildren()
    {
        return $this->children;
    }
    public function isLeaf()
    {
        return count($this->children) === 0;
    }
    public function getPathToRoot()
    {
        $path = array();
        $node = $this;
        do {
            $path[] = $node;
            $node = $node->parent;
        } while ($node != null);
        return $path;
    }
    public function __toString()
    {
        return spl_object_hash($this);
    }
}
