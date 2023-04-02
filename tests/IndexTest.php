<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Aggregations;
use Francerz\PowerData\Index;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $index;

    public function __construct()
    {
        parent::__construct();
        $this->index = new Index(array(
            ['col1' => 1, 'col2' => 1], # 0
            ['col1' => 2, 'col2' => 2], # 1
            ['col1' => 3, 'col2' => 2], # 2
            ['col1' => 4, 'col2' => 1], # 3
            ['col1' => 5, 'col2' => 4], # 4
            ['col1' => 6, 'col2' => 3], # 5
            ['col1' => 7, 'col2' => 2], # 6
            ['col1' => 8, 'col2' => 1], # 7
            ['col1' => 9, 'col2' => null] # 8
        ), ['col1','col2']);
    }
    public function testFindAllKeys()
    {
        $index = $this->index;

        $this->assertEquals([], array_values($index->findAllKeys(['col1' => 0])));
        $this->assertEquals([0], array_values($index->findAllKeys(['col1' => 1])));
        $this->assertEquals([1], array_values($index->findAllKeys(['col1' => 2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1' => 3])));
        $this->assertEquals([3], array_values($index->findAllKeys(['col1' => 4])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col1' => 5])));
        $this->assertEquals([5], array_values($index->findAllKeys(['col1' => 6])));
        $this->assertEquals([6], array_values($index->findAllKeys(['col1' => 7])));
        $this->assertEquals([7], array_values($index->findAllKeys(['col1' => 8])));
        $this->assertEquals([8], array_values($index->findAllKeys(['col1' => 9])));
        $this->assertEquals([], array_values($index->findAllKeys(['col1' => 10])));

        $this->assertEquals([], array_values($index->findAllKeys(['col2' => 0])));
        $this->assertEquals([0,3,7], array_values($index->findAllKeys(['col2' => 1])));
        $this->assertEquals([1,2,6], array_values($index->findAllKeys(['col2' => 2])));
        $this->assertEquals([5], array_values($index->findAllKeys(['col2' => 3])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col2' => 4])));
        $this->assertEquals([], array_values($index->findAllKeys(['col2' => 5])));

        $this->assertEquals([1], array_values($index->findAllKeys(['col1' => 2, 'col2' => 2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1' => 3, 'col2' => 2])));
        $this->assertEquals([], array_values($index->findAllKeys(['col1' => 4,'col2' => 2])));

        // ADD multiple values column values
        $this->assertEquals([0, 3, 4, 7], array_values($index->findAllKeys(['col2' => [1, 4]])));

        $this->assertEquals([8], array_values($index->findAllKeys(['col2' => null])));
    }

    public function testMutableIndex()
    {
        $index = new Index(array(
            ['col1' => 1, 'col2' => 1], # 0
            ['col1' => 2, 'col2' => 2], # 1
            ['col1' => 3, 'col2' => 2], # 2
            ['col1' => 4, 'col2' => 1], # 3
        ), ['col1']);

        $index->add(['col1' => 5, 'col2' => 3]); # 4

        $this->assertEquals([0], array_values($index->findAllKeys(['col1' => 1])));
        $this->assertEquals([1], array_values($index->findAllKeys(['col1' => 2])));
        $this->assertEquals([2], array_values($index->findAllKeys(['col1' => 3])));
        $this->assertEquals([3], array_values($index->findAllKeys(['col1' => 4])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col1' => 5])));

        $this->assertEquals([1,2,3,4,5], $index->getColumnValues('col1'));

        $index->addColumn('col2');

        $this->assertEquals([0,3], array_values($index->findAllKeys(['col2' => 1])));
        $this->assertEquals([1,2], array_values($index->findAllKeys(['col2' => 2])));
        $this->assertEquals([4], array_values($index->findAllKeys(['col2' => 3])));

        $this->assertEquals([1,2,3], $index->getColumnValues('col2'));
        $this->assertEmpty($index->getColumnValues('col3'));
    }

    public function testGroupBy()
    {
        $groups = $this->index->groupBy('col2');

        $this->assertEquals(5, count($groups));
        $this->assertEquals(8, max(array_column($groups[1], 'col1')));
        $this->assertEquals(7, max(array_column($groups[2], 'col1')));
        $this->assertEquals(6, max(array_column($groups[3], 'col1')));
    }

    public function testAggregate()
    {
        $data = [
            ['pk' => 1, 'fk' => 1, 'amount' => 5, 'seconds' => 10],
            ['pk' => 2, 'fk' => 2, 'amount' => 12, 'seconds' => 21],
            ['pk' => 3, 'fk' => 1, 'amount' => 8, 'seconds' => 12],
            ['pk' => 4, 'fk' => 1, 'amount' => 10, 'seconds' => 15],
            ['pk' => 5, 'fk' => 2, 'amount' => 3, 'seconds' => 8],
            ['pk' => 6, 'fk' => 3, 'amount' => 10, 'seconds' => 11],
            ['pk' => 7, 'fk' => 5, 'amount' => 9, 'seconds' => 18]
        ];
        $index = new Index($data, ['pk', 'fk']);

        $this->assertEquals(7, $index->aggregate('count'));

        $this->assertEquals(3, $index->aggregate('min', 'amount'));
        $this->assertEquals(12, $index->aggregate('max', 'amount'));
        $this->assertEquals(57, $index->aggregate('array_sum', 'amount'));

        $this->assertEquals(8, $index->aggregate('min', 'seconds'));
        $this->assertEquals(21, $index->aggregate('max', 'seconds'));
        $this->assertEquals(95, $index->aggregate('array_sum', 'seconds'));

        $this->assertEquals(5, $index->aggregate('min', 'amount', ['fk' => 1]));
        $this->assertEquals(10, $index->aggregate('max', 'amount', ['fk' => 1]));
        $this->assertEquals(3, $index->aggregate('count', 'amount', ['fk' => 1]));
        $this->assertEquals(23, $index->aggregate('array_sum', 'amount', ['fk' => 1]));

        $this->assertEquals(3, $index->aggregate('min', 'amount', ['fk' => [1, 2]]));
        $this->assertEquals(12, $index->aggregate('max', 'amount', ['fk' => [1, 2]]));
        $this->assertEquals(5, $index->aggregate('count', 'amount', ['fk' => [1, 2]]));
        $this->assertEquals(38, $index->aggregate('array_sum', 'amount', ['fk' => [1, 2]]));

        // Filter that returns 0 items causes null. (Coalesce operator ?? handle this).
        $this->assertSame(null, $index->aggregate('min', 'amount', ['fk' => 4]));
        $this->assertSame(null, $index->aggregate('max', 'amount', ['fk' => 4]));
        $this->assertSame(null, $index->aggregate('array_sum', 'amount', ['fk' => 4]));

        // Accessing unknown column causes null.
        $this->assertSame(null, $index->aggregate('min', 'quantity'));

        $this->assertEquals(3, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [0]));
        $this->assertEquals(4.8, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [15]));
        $this->assertEquals(6.5, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [25]));
        $this->assertEquals(9, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [50]));
        $this->assertEquals(10, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [75]));
        $this->assertEquals(10.2, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [85]));
        $this->assertEquals(12, $index->aggregate([Aggregations::class, 'percentile'], 'amount', null, [100]));
    }
}
