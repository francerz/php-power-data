<?php

namespace Francerz\PowerData;

abstract class Matrix
{
    static public function transpose(array $matrix)
    {
        $out = [];
        foreach ($matrix as $name => $col) {
            foreach ($col as $i =>$cell) {
                $out[$i][$name] = $cell;
            }
        }
        return $out;
    }

    static public function transposeFill(array $matrix, $emptyFill = null)
    {
        $out = [];
        $maxI = 0;
        foreach ($matrix as $name => $col) {
            if (!is_array($col)) continue;
            foreach ($col as $i => $cell) {
                $out[$i][$name] = $cell;
                if ($i > $maxI) $maxI = $i;
            }
        }
        foreach ($matrix as $name => $col) {
            if (is_array($col)) continue;
            for ($i = 0; $i <= $maxI; $i++) {
                $out[$i][$name] = $col;
            }
        }
        return $out;
    }
}