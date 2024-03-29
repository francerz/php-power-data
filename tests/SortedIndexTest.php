<?php

namespace Francerz\PowerData\Tests;

use Francerz\PowerData\SortedIndex;
use PHPUnit\Framework\TestCase;

class SortedIndexTest extends TestCase
{
    public function testContains()
    {
        $index = new SortedIndex([8, 7, 2, 1, 45, 932, 23, 135, 85, 585, 238, 246, 0, 5, 10, 24, 856]);

        $this->assertTrue($index->contains(8, $j1));
        $this->assertTrue($index->contains(23, $j2));
        $this->assertFalse($index->contains(3, $j3));

        // print json_encode([$j1, $j2, $j3]);
    }

    public function testIntersect()
    {
        $index0 = new SortedIndex(range(0, 5));
        $index1 = new SortedIndex(range(1, 10));
        $index2 = new SortedIndex(range(-10, 3));
        $actual = SortedIndex::intersect([$index0, $index1, $index2]);
        $this->assertEquals([1,2,3], $actual);
    }

    private function intersectPerformance($iterations)
    {
        $periodo_id = range(0, 15000);
        $materia_id = range(1, 50);
        $alumno_id = range(-4, 3);

        $sortedStart = microtime(true);
        $indexPeriodo = new SortedIndex($periodo_id);
        $indexMateria = new SortedIndex($materia_id);
        $indexAlumno = new SortedIndex($alumno_id);
        for ($i = 0; $i < $iterations; $i++) {
            $resultSorted = SortedIndex::intersect([$indexPeriodo, $indexMateria, $indexAlumno]);
        }
        $sortedEnd = microtime(true);

        $arrayStart = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $resultArray = array_intersect($periodo_id, $materia_id, $alumno_id);
        }
        $arrayEnd = microtime(true);

        $resultArray = array_values($resultArray);
        $resultSorted = array_values($resultSorted);
        $this->assertEquals($resultArray, $resultSorted);

        $arrayDiff = 0;
        $arrayDiff = $arrayEnd - $arrayStart;
        $sortedDiff = $sortedEnd - $sortedStart;
        return [
            'Total iterations' => $iterations,
            'Time array_intersect' => $arrayDiff,
            'Time SortedIndex::intersect' => $sortedDiff,
            'Times ratio_difference' => $arrayDiff / $sortedDiff,
        ];
    }

    public function atestIntersectPerformance()
    {
        $data[] = $this->intersectPerformance(1);
        $data[] = $this->intersectPerformance(10);
        $data[] = $this->intersectPerformance(100);
        // $data[] = $this->intersectPerformance(1000);
        // $data[] = $this->intersectPerformance(10000);
        // $data[] = $this->intersectPerformance(100000);
        // $data[] = $this->intersectPerformance(1000000);

        print_r($data);
    }
}
