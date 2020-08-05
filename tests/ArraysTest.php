<?php

use Francerz\PowerData\Arrays;
use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{
    public function testHasNumericKeys_EmptyArray()
    {
        $input = [];
        $actual = Arrays::hasNumericKeys($input);
        $this->assertFalse($actual);
        return $input;
    }
    /**
     * @depends testHasNumericKeys_EmptyArray
     */
    public function testHasNumericKeys_SimpleArray(array $input)
    {
        $input[] = 'foo';
        $input[] = 'bar';
        $actual = Arrays::hasNumericKeys($input);
        $this->assertTrue($actual);
    }
    /**
     * @depends testHasNumericKeys_EmptyArray
     */
    public function testHasNumericKeys_ExplicitNumericKeys(array $input)
    {
        $input[1] = 'foo';
        $input[2] = 'bar';
        $actual = Arrays::hasNumericKeys($input);
        $this->assertTrue($actual);
    }
    /**
     * @depends testHasNumericKeys_EmptyArray
     */
    public function testHasNumericKeys_ExplicitOnlyStringKeys(array $input)
    {
        $input['a'] = 'foo';
        $input['b'] = 'bar';
        $actual = Arrays::hasNumericKeys($input);
        $this->assertFalse($actual);
        return $input;
    }
    /**
     * @depends testHasNumericKeys_ExplicitOnlyStringKeys
     */
    public function testHasNumericKeys_ExplicitAnyNumericKey(array $input)
    {
        $input[] = 'tic';
        $actual = Arrays::hasNumericKeys($input);
        $this->assertTrue($actual);
    }

    public function testHasStringKeys_EmptyArray()
    {
        $input = [];
        $actual = Arrays::hasStringKeys($input);
        $this->assertFalse($actual);
        return $input;
    }
    /**
     * @depends testHasStringKeys_EmptyArray
     */
    public function testHasStringKeys_SimpleArray(array $input)
    {
        $input[] = 'foo';
        $input[] = 'bar';
        $actual = Arrays::hasStringKeys($input);
        $this->assertFalse($actual);
    }
    /**
     * @depends testHasStringKeys_EmptyArray
     */
    public function testHasStringKeys_ExplicitNumericKeys(array $input)
    {
        $input[1] = 'foo';
        $input[2] = 'bar';
        $actual = Arrays::hasStringKeys($input);
        $this->assertFalse($actual);
        return $input;
    }
    /**
     * @depends testHasStringKeys_EmptyArray
     */
    public function testHasStringKeys_ExplicitStringKeys(array $input)
    {
        $input['a'] = 'foo';
        $input['b'] = 'bar';
        $actual = Arrays::hasStringKeys($input);
        $this->assertTrue($actual);
    }
    /**
     * @depends testHasStringKeys_ExplicitNumericKeys
     */
    public function testHasStringKeys_ExplicitAnyStringKey(array $input)
    {
        $input['foo'] = 'bar';
        $actual = Arrays::hasStringKeys($input);
        $this->assertTrue($actual);
    }


    public function testIntersect_numeric()
    {
        $input1 = [1, 2, 3, 4, 5, 6];
        $input2 = [2, 4, 6, 8];
        $expected = [1=>2, 3=>4, 5=>6];

        $result = Arrays::intersect($input1, $input2);

        $this->assertEquals($expected, $result);
    }

    public function testIntersect_empty()
    {
        $result = Arrays::intersect([1], []);
        $this->assertEquals([], $result);
    }

    public function testIntersect_string()
    {
        $input1 = ['a','b','c'];
        $input2 = ['b','d','f'];
        $expected = [1=>'b'];
        $result = Arrays::intersect($input1, $input2);

        $this->assertEquals($expected, $result);
    }

    public function testKeyInsensitive() : array
    {
        $input = ['A'=>65, 'B'=> 66, 'C'=>67];

        $this->assertEquals('A', Arrays::keyInsensitive($input, 'A'));
        $this->assertEquals('A', Arrays::keyInsensitive($input, 'a'));
        $this->assertEquals('B', Arrays::keyInsensitive($input, 'B'));
        $this->assertEquals('B', Arrays::keyInsensitive($input, 'b'));
        $this->assertEquals('C', Arrays::keyInsensitive($input, 'C'));
        $this->assertEquals('C', Arrays::keyInsensitive($input, 'c'));

        return $input;
    }

    /**
     * 
     * @depends testKeyInsensitive
     *
     * @param array $input
     * @return void
     */
    public function testValueKeyInsensitive(array $input)
    {
        $this->assertEquals(65, Arrays::valueKeyInsensitive($input,'A'));
        $this->assertEquals(65, Arrays::valueKeyInsensitive($input,'a'));
        $this->assertEquals(66, Arrays::valueKeyInsensitive($input,'B'));
        $this->assertEquals(66, Arrays::valueKeyInsensitive($input,'b'));
        $this->assertEquals(67, Arrays::valueKeyInsensitive($input,'C'));
        $this->assertEquals(67, Arrays::valueKeyInsensitive($input,'c'));
    }
}