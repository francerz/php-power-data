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
        $this->assertEquals(2.4, Aggregations::percentile($data, 5));
        $this->assertEquals(4, Aggregations::percentile($data, 25));
        $this->assertEquals(8, Aggregations::percentile($data, 50));
        $this->assertEquals(14, Aggregations::percentile($data, 75));
        $this->assertEquals(14.8, Aggregations::percentile($data, 95));
        $this->assertEquals(15, Aggregations::percentile($data, 100));

        $this->assertEquals(0, Aggregations::findPercentile($data, 2));
        $this->assertEquals(5, Aggregations::findPercentile($data, 2.4));
        $this->assertEquals(25, Aggregations::findPercentile($data, 4));
        $this->assertEquals(50, Aggregations::findPercentile($data, 8));
        $this->assertEquals(75, Aggregations::findPercentile($data, 14));
        $this->assertEquals(95, Aggregations::findPercentile($data, 14.8));
        $this->assertEquals(100, Aggregations::findPercentile($data, 15));

        $this->assertEquals('4, 8, 15, 14, 2', Aggregations::concat($data, ', '));
    }

    public function testPercentileFlags()
    {
        $data = [1, 1, 1, 2, 2, 2, 2, 4, 5, 5, 5, 8, 8];

        $this->assertEquals(13, Aggregations::count($data));
        $this->assertEquals(1, Aggregations::percentile($data, 0));
        $this->assertEquals(8, Aggregations::percentile($data, 100));
        $this->assertEquals(3, Aggregations::percentile($data, 54.16666666666));
        $this->assertEquals(2, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_FIRST));
        // Higher has no difference in execution so its the same as Lower.
        $this->assertEquals(2, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_LAST));
        // Unique: 1, 2, 4, 5, 8
        $this->assertEquals(4, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_UNIQUE));
        $this->assertEquals(4, Aggregations::percentile(
            $data,
            50,
            Aggregations::PERCENTILE_FLAGS_LAST | Aggregations::PERCENTILE_FLAGS_UNIQUE
        ));

        $this->assertEquals(00.0000000000, Aggregations::findPercentile($data, 0, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(00.0000000000, Aggregations::findPercentile($data, 1, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(25.0000000000, Aggregations::findPercentile($data, 2, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(54.1666666666, Aggregations::findPercentile($data, 3, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(58.3333333333, Aggregations::findPercentile($data, 4, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(66.6666666667, Aggregations::findPercentile($data, 5, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(86.1111111111, Aggregations::findPercentile($data, 6, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(88.8888888888, Aggregations::findPercentile($data, 7, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(91.6666666667, Aggregations::findPercentile($data, 8, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(100.000000000, Aggregations::findPercentile($data, 9, Aggregations::PERCENTILE_FLAGS_FIRST));
        $this->assertEquals(100.000000000, Aggregations::findPercentile($data, 10, Aggregations::PERCENTILE_FLAGS_FIRST));

        $this->assertEquals(00.0000000000, Aggregations::findPercentile($data, 0, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(16.6666666667, Aggregations::findPercentile($data, 1, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(50.0000000000, Aggregations::findPercentile($data, 2, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(54.1666666666, Aggregations::findPercentile($data, 3, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(58.3333333333, Aggregations::findPercentile($data, 4, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(83.3333333333, Aggregations::findPercentile($data, 5, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(86.1111111111, Aggregations::findPercentile($data, 6, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(88.8888888888, Aggregations::findPercentile($data, 7, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(100.000000000, Aggregations::findPercentile($data, 8, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(100.000000000, Aggregations::findPercentile($data, 9, Aggregations::PERCENTILE_FLAGS_LAST));
        $this->assertEquals(100.000000000, Aggregations::findPercentile($data, 10, Aggregations::PERCENTILE_FLAGS_LAST));
    }
}
