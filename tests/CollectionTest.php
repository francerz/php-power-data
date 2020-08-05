<?php

use Francerz\PowerData\Collection;
use Francerz\PowerData\Exceptions\InvalidOffsetException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testConstructor_Empty()
    {
        $collection = new Collection();

        $this->assertEmpty($collection);
        return $collection;
    }

    /**
     * @depends testConstructor_Empty
     */
    public function testOffsetSet_EmptyOffset(Collection $collection)
    {
        $collection[] = 0;

        $this->assertNotEmpty($collection);
        $this->assertEquals(1, $collection->count());

        return $collection;
    }

    /**
     * @depends testOffsetSet_EmptyOffset
     */
    public function testOffsetSet_NumericOffset(Collection $collection)
    {
        $collection[1] = 1;
        $this->assertEquals(2, $collection->count());
        return $collection;
    }

    /**
     * @depends testOffsetSet_NumericOffset
     */
    public function testOffsetSet_StringOffset(Collection $collection)
    {
        $this->expectException(InvalidOffsetException::class);
        $collection['a'] = 2;
    }

    /**
     * @depends testOffsetSet_NumericOffset
     */
    public function testOffsetExists(Collection $collection)
    {
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertFalse(isset($collection[2]));
    }

    /**
     * @depends testOffsetSet_NumericOffset
     */
    public function testOffsetGet(Collection $collection)
    {
        $this->assertEquals(0, $collection[0]);
        $this->assertEquals(1, $collection[1]);

        $this->expectException(InvalidOffsetException::class);
        $a = $collection['a'];
    }
}