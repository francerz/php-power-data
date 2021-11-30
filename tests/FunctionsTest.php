<?php

use Francerz\PowerData\Functions;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testTestSignature()
    {
        // test empty callable function.
        $this->assertTrue(
            Functions::testSignature(function(){})
        );

        $this->assertTrue(
            Functions::testSignature(function(string $a) {}, ['string'], 'void')
        );

        $this->assertTrue(
            Functions::testSignature(
                function(string $a) : string{ return $a; },
                ['string'],
                'string'
            )
        );

        $this->assertTrue(
            Functions::testSignature(
                function(string $a, stdClass $obj) : stdClass { return $obj; },
                ['string', stdClass::class],
                stdClass::class
            )
        );

        $this->assertTrue(
            Functions::testSignature(
                function(FunctionsTest $a) : TestCase { return $a; },
                [TestCase::class],
                TestCase::class
            )
        );
    }
}
