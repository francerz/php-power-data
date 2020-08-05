<?php

namespace Francerz\PowerData;

use Exception;

class TreeNode
{
    private Tree $tree;
    private ?TreeNode $parent = null;
    private $value;
    private $children = array();

    public function __construct(Tree $tree, $value)
    {
        $this->value = $value;
        $this->tree = $tree;
    }
    public function setParent(TreeNode $newParent) : void
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
    public function unsetParent() : void
    {
        if (isset($this->parent)) {
            Arrays::remove($this->parent->children, $this);
        }
    }
    public function getTree() : Tree
    {
        return $this->tree;
    }
    public function getParent() : TreeNode
    {
        return $this->parent;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getChildren() : array
    {
        return $this->children;
    }
    public function isLeaf() : bool
    {
        return count($this->children) === 0;
    }
    public function getPathToRoot(): array
    {
        $path = array();
        $node = $this;
        do 
        {
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