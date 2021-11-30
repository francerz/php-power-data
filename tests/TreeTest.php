<?php

use Francerz\PowerData\Tree;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    public function testFromArray()
    {
        $array = array(
            ['pk' => 1],
            ['pk' => 2, 'fk' => 1],
            ['pk' => 3, 'fk' => 1],
            ['pk' => 4, 'fk' => 3]
        );
        $tree = Tree::fromArray($array, 'pk', 'fk');

        $this->assertEquals(['pk' => 1], $tree->getRoot()->getValue());
    }
}
