<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\Matrix;
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    public function testTranspose()
    {
        $matrix = Matrix::transpose([
            'qty' => [1, 2, 3],
            'price' => [10, 20, 30]
        ]);

        $expected = [
            ['qty' => 1, 'price' => 10],
            ['qty' => 2, 'price' => 20],
            ['qty' => 3, 'price' => 30]
        ];

        $this->assertEquals($expected, $matrix);
    }

    public function testTransposeFill()
    {
        $matrix = Matrix::transposeFill([
            'sell_id'   => 1,
            'qty' => [1, 2, 3],
            'price' => [10, 20, 30]
        ]);

        $expected = [
            ['qty' => 1, 'price' => 10, 'sell_id' => 1],
            ['qty' => 2, 'price' => 20, 'sell_id' => 1],
            ['qty' => 3, 'price' => 30, 'sell_id' => 1]
        ];

        $this->assertEquals($expected, $matrix);
    }
}
