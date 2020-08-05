<?php

use Francerz\PowerData\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testInvalidType()
    {
        $this->expectException(Exception::class);
        Type::for("Dos_Uno@_");
    }
    public function testInt()
    {
        $type = Type::for('int');

        $this->assertTrue($type->isKnownType());
        $this->assertTrue($type->isPrimitive());
        $this->assertFalse($type->isClass());
        $this->assertFalse($type->isArray());

        $this->assertEquals('int', $type->getType());

        $this->assertTrue($type->check(1));
        $this->assertFalse($type->check('1'));
    }
    public function testClass()
    {
        $type = Type::for(Type::class);

        $this->assertFalse($type->isKnownType());
        $this->assertFalse($type->isPrimitive());
        $this->assertTrue($type->isClass());
        $this->assertFalse($type->isArray());

        $this->assertEquals(Type::class, $type->getType());
        $this->assertEquals('Francerz\PowerData', $type->getNamespace());
        $this->assertEquals('Type', $type->getClassName());

        $this->assertTrue($type->check($type));
    }

    public function testIntArray()
    {
        $type = Type::for('int[]');
        $altType = Type::for('int', 1);

        $this->assertEquals($type, $altType);

        $this->assertTrue($type->isKnownType());
        $this->assertTrue($type->isPrimitive());
        $this->assertFalse($type->isClass());
        $this->assertTrue($type->isArray());

        $this->assertEquals('int', $type->getType());

        $this->assertTrue($type->check([1, 2, 3]));
        $this->assertFalse($type->check(['foo','bar']));
        $this->assertFalse($type->check('abc'));
        $this->assertFalse($type->check(1));
    }
    public function testStringArray()
    {
        $type = Type::for('string[]');
        $altType = Type::for('string', 1);

        $this->assertEquals($type, $altType);

        $this->assertTrue($type->isKnownType());
        $this->assertTrue($type->isPrimitive());
        $this->assertFalse($type->isClass());
        $this->assertTrue($type->isArray());

        $this->assertEquals('string', $type->getType());

        $this->assertTrue($type->check(['foo','bar']));
        $this->assertFalse($type->check([1, 2, 3]));
        $this->assertFalse($type->check('abc'));
        $this->assertFalse($type->check(1));
    }
    public function testIntMatrix()
    {
        $type = Type::for('int[][]');
        $altType = Type::for('int', 2);

        $this->assertEquals($type, $altType);

        $this->assertTrue($type->isKnownType());
        $this->assertTrue($type->isPrimitive());
        $this->assertFalse($type->isClass());
        $this->assertTrue($type->isArray());

        $this->assertEquals('int', $type->getType());
        $this->assertEquals(2, $type->getArrayDepth());

        $this->assertTrue($type->check([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]));
        $this->assertFalse($type->check([1, 2, 3]));
        $this->assertFalse($type->check([
            ['foo'],
            ['bar']
        ]));
        $this->assertFalse($type->check([
            [1, 2],
             3, 4,
            [5, 6]
        ]));
    }
    public function testTypeOf()
    {
        $type = Type::of(1);

        $this->assertEquals('int', $type->getType());
        $this->assertFalse($type->isArray());

        $type = Type::of('1');

        $this->assertEquals('string', $type->getType());
        $this->assertFalse($type->isArray());

        $type = Type::of($type);

        $this->assertEquals(Type::class, $type->getType());
        $this->assertFalse($type->isArray());

        $type = Type::of([1, 2, 3]);

        $this->assertEquals('int[]', $type->__toString());
        $this->assertEquals('int', $type->getType());
        $this->assertTrue($type->isArray());
        $this->assertEquals(1, $type->getArrayDepth());

        $type = Type::of([[1, 2], [3, 4]]);

        $this->assertEquals('int[][]', $type->__toString());
        $this->assertEquals('int', $type->getType());
        $this->assertTrue($type->isArray());
        $this->assertEquals(2, $type->getArrayDepth());

        $type = Type::of([]);

        $this->assertEquals('mixed[]', $type->__toString());

        $type = Type::of([1, .2]);

        $this->assertEquals('number[]', $type->__toString());

        $type = Type::of([1, [1, 2]]);
        $this->assertEquals('int[][]', $type->__toString());

        $type = Type::of([1, [[2, 3]]]);
        $this->assertEquals('int[][]', $type->__toString());
    }
}