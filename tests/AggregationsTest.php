<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Aggregations;
use PHPUnit\Framework\TestCase;

class AggregationsTest extends TestCase
{
    private const FLOAT_DELTA = 0.00000001;

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

        $this->assertEqualsWithDelta(2, Aggregations::percentile($data, 0), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(2.4, Aggregations::percentile($data, 5), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(4, Aggregations::percentile($data, 25), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(8, Aggregations::percentile($data, 50), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(14, Aggregations::percentile($data, 75), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(14.8, Aggregations::percentile($data, 95), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(15, Aggregations::percentile($data, 100), self::FLOAT_DELTA);

        $this->assertEqualsWithDelta(0, Aggregations::findPercentile($data, 2), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(5, Aggregations::findPercentile($data, 2.4), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(25, Aggregations::findPercentile($data, 4), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(50, Aggregations::findPercentile($data, 8), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(75, Aggregations::findPercentile($data, 14), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(95, Aggregations::findPercentile($data, 14.8), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100, Aggregations::findPercentile($data, 15), self::FLOAT_DELTA);

        $this->assertEqualsWithDelta('4, 8, 15, 14, 2', Aggregations::concat($data, ', '), self::FLOAT_DELTA);
    }

    public function testPercentileFlags()
    {
        $data = [1, 1, 1, 2, 2, 2, 2, 4, 5, 5, 5, 8, 8];

        $this->assertEqualsWithDelta(13, Aggregations::count($data), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(1, Aggregations::percentile($data, 0), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(8, Aggregations::percentile($data, 100), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(3, Aggregations::percentile($data, 54.16666666666), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(2, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        // Higher has no difference in execution so its the same as Lower.
        $this->assertEqualsWithDelta(2, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        // Unique: 1, 2, 4, 5, 8
        $this->assertEqualsWithDelta(4, Aggregations::percentile($data, 50, Aggregations::PERCENTILE_FLAGS_UNIQUE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(4, Aggregations::percentile(
            $data,
            50,
            Aggregations::PERCENTILE_FLAGS_LAST | Aggregations::PERCENTILE_FLAGS_UNIQUE
        ), self::FLOAT_DELTA);

        $this->assertEqualsWithDelta(00.0000000000, Aggregations::findPercentile($data, 0, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(00.0000000000, Aggregations::findPercentile($data, 1, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(25.0000000000, Aggregations::findPercentile($data, 2, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(54.1666666666, Aggregations::findPercentile($data, 3, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(58.3333333333, Aggregations::findPercentile($data, 4, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(66.6666666667, Aggregations::findPercentile($data, 5, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(86.1111111111, Aggregations::findPercentile($data, 6, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(88.8888888888, Aggregations::findPercentile($data, 7, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(91.6666666667, Aggregations::findPercentile($data, 8, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 9, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 10, Aggregations::PERCENTILE_FLAGS_FIRST), self::FLOAT_DELTA);

        $this->assertEqualsWithDelta(00.0000000000, Aggregations::findPercentile($data, 0, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(8.33333333333, Aggregations::findPercentile($data, 1, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(37.5000000000, Aggregations::findPercentile($data, 2, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(54.1666666666, Aggregations::findPercentile($data, 3, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(58.3333333333, Aggregations::findPercentile($data, 4, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(75.0000000000, Aggregations::findPercentile($data, 5, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(86.1111111111, Aggregations::findPercentile($data, 6, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(88.8888888888, Aggregations::findPercentile($data, 7, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(95.8333333334, Aggregations::findPercentile($data, 8, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 9, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 10, Aggregations::PERCENTILE_FLAGS_MIDDLE), self::FLOAT_DELTA);

        $this->assertEqualsWithDelta(00.0000000000, Aggregations::findPercentile($data, 0, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(16.6666666667, Aggregations::findPercentile($data, 1, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(50.0000000000, Aggregations::findPercentile($data, 2, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(54.1666666666, Aggregations::findPercentile($data, 3, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(58.3333333333, Aggregations::findPercentile($data, 4, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(83.3333333333, Aggregations::findPercentile($data, 5, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(86.1111111111, Aggregations::findPercentile($data, 6, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(88.8888888888, Aggregations::findPercentile($data, 7, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 8, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 9, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
        $this->assertEqualsWithDelta(100.000000000, Aggregations::findPercentile($data, 10, Aggregations::PERCENTILE_FLAGS_LAST), self::FLOAT_DELTA);
    }
}
