<?php

use Francerz\PowerData\Index;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $index;

    public function __construct()
    {
        parent::__construct();
        $this->index = new Index(array(
            ['col1'=>1, 'col2'=>1], # 0
            ['col1'=>2, 'col2'=>2], # 1
            ['col1'=>3, 'col2'=>2], # 2
            ['col1'=>4, 'col2'=>1], # 3
            ['col1'=>5, 'col2'=>4], # 4
            ['col1'=>6, 'col2'=>3], # 5
            ['col1'=>7, 'col2'=>2], # 6
            ['col1'=>8, 'col2'=>1], # 7
        ),['col1','col2']);
    }
    public function testFindAllKeys()
    {
        $index = $this->index;

        $this->assertEquals([], array_values($index->findAllKeys(['col1'=>0])));
        $this->assertEquals([0], array_values($index->findAllKeys(['col1'=>1])));
        $this->assertEquals([1], array_values($index->findAllKeys(['col1'=>2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1'=>3])));
        $this->assertEquals([3], array_values($index->findAllKeys(['col1'=>4])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col1'=>5])));
        $this->assertEquals([5], array_values($index->findAllKeys(['col1'=>6])));
        $this->assertEquals([6], array_values($index->findAllKeys(['col1'=>7])));
        $this->assertEquals([7], array_values($index->findAllKeys(['col1'=>8])));
        $this->assertEquals([], array_values($index->findAllKeys(['col1'=>9])));

        $this->assertEquals([], array_values($index->findAllKeys(['col2'=>0])));
        $this->assertEquals([0,3,7], array_values($index->findAllKeys(['col2'=>1])));
        $this->assertEquals([1,2,6], array_values($index->findAllKeys(['col2'=>2])));
        $this->assertEquals([5], array_values($index->findAllKeys(['col2'=>3])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col2'=>4])));
        $this->assertEquals([], array_values($index->findAllKeys(['col2'=>5])));

        $this->assertEquals([1], array_values($index->findAllKeys(['col1'=>2,'col2'=>2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1'=>3,'col2'=>2])));
        $this->assertEquals([], array_values($index->findAllKeys(['col1'=>4,'col2'=>2])));
    }

    public function testMutableIndex()
    {
        $index = new Index(array(
            ['col1'=>1, 'col2'=>1], # 0
            ['col1'=>2, 'col2'=>2], # 1
            ['col1'=>3, 'col2'=>2], # 2
            ['col1'=>4, 'col2'=>1], # 3
        ), ['col1']);

        $index->add(['col1'=>5, 'col2'=>3]); # 4

        $this->assertEquals([0], array_values($index->findAllKeys(['col1'=>1])));
        $this->assertEquals([1], array_values($index->findAllKeys(['col1'=>2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1'=>3])));
        $this->assertEquals([3], array_values($index->findAllKeys(['col1'=>4])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col1'=>5])));

        $this->assertEquals([1,2,3,4,5], $index->getColumnValues('col1'));

        $index->addColumn('col2');

        $this->assertEquals([0,3], array_values($index->findAllKeys(['col2'=>1])));
        $this->assertEquals([1,2], array_values($index->findAllKeys(['col2'=>2])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col2'=>3])));

        $this->assertEquals([1,2,3], $index->getColumnValues('col2'));
        $this->assertEmpty($index->getColumnValues('col3'));
    }

    public function testGroupBy()
    {
        $groups = $this->index->groupBy('col2');

        $this->assertEquals(4, count($groups));
        $this->assertEquals(8, max(array_column($groups[1], 'col1')) );
        $this->assertEquals(7, max(array_column($groups[2], 'col1')) );
        $this->assertEquals(6, max(array_column($groups[3], 'col1')) );
    }
}
