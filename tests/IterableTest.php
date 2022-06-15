<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Iterables;
use PHPUnit\Framework\TestCase;

class IterableTest extends TestCase
{
    public function testFilterColumns()
    {
        $a = json_decode(json_encode([
            ['col1' => 1, 'col2' => 1],
            ['col1' => 2, 'col2' => 1],
            ['col1' => 3, 'col2' => 2]
        ]));

        $this->assertEquals(
            [['col1' => 1],['col1' => 2],['col1' => 3]],
            Iterables::filterColumns($a, ['col1'])
        );
        $this->assertEquals(
            [['a' => 1],['a' => 2],['a' => 3]],
            Iterables::filterColumns($a, ['col1'], ['a'])
        );
        $this->assertEquals(
            [['a' => 1],['a' => 1],['a' => 2]],
            Iterables::filterColumns($a, ['col2'], ['a'])
        );
    }

    // public function testCompare()
    // {
    //     $a = json_decode(json_encode([
    //         ['a' => 1],
    //         ['a' => 2],
    //         ['a' => 3]
    //     ]));
    //     $b = json_decode(json_encode([
    //         ['b' => 2]
    //     ]));

    //     $this->assertEquals([], Iterables::diff($a, $b, function ($a, $b) {
    //         return $a->a == $b->b ? 0 : ($a->a > $b->b ? 1 : -1);
    //     }));
    // }
}
