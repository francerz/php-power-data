<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Multimap;
use PHPUnit\Framework\TestCase;

class MultimapTest extends TestCase
{
    public function testAdd()
    {
        $mm = new Multimap();
        $mm->add('a', 0);
        $mm->add('b', 1);
        $mm->add('a', 2, 3);
        $mm->add('b', 4, 5);

        $this->assertEquals([0,2,3], $mm->get('a'));
        $this->assertEquals([1,4,5], $mm->get('b'));

        return $mm;
    }

    /**
     * @depends testAdd
     */
    public function testSet(Multimap $mm)
    {
        $mm->set('a', 6, 7);
        $this->assertEquals([6, 7], $mm->get('a'));
    }
}
