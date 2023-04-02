<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Aggregations;
use PHPUnit\Framework\TestCase;

class AggregationsTest extends TestCase
{
    public function testAggregations()
    {
        $data = [4, 8, 15, 14, 2];

        $this->assertEquals(5, Aggregations::count($data));
        $this->assertEquals(43, Aggregations::sum($data));
        $this->assertEquals([], Aggregations::mode($data));
        $this->assertEquals(8.6, Aggregations::mean($data));
        $this->assertEquals(13440, Aggregations::product($data));

        $this->assertEquals(4, Aggregations::first($data));
        $this->assertEquals(2, Aggregations::last($data));

        $this->assertEquals(2, Aggregations::min($data));
        $this->assertEquals(8, Aggregations::median($data));
        $this->assertEquals(15, Aggregations::max($data));

        $this->assertEquals(2, Aggregations::percentile($data, 0));
        $this->assertEquals(4, Aggregations::percentile($data, 25));
        $this->assertEquals(8, Aggregations::percentile($data, 50));
        $this->assertEquals(14, Aggregations::percentile($data, 75));
        $this->assertEquals(15, Aggregations::percentile($data, 100));

        $this->assertEquals('4, 8, 15, 14, 2', Aggregations::concat($data, ', '));
    }
}
