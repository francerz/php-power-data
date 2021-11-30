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

    public function testKeyInsensitive()
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


    public function testNest()
    {
        $groups = array(
            ['group_id'=>'1', 'signature'=>'Programming basics'],
            ['group_id'=>'2', 'signature'=>'Computing maths']
        );
        $students = array(
            ['student_id'=>'1', 'name'=>'John Doe', 'group_id'=>'1'],
            ['student_id'=>'2', 'name'=>'Jane Doe', 'group_id'=>'2'],
            ['student_id'=>'3', 'name'=>'James Doe', 'group_id'=>'2'],
                ['student_id'=>'4', 'name'=>'Judy Doe', 'group_id'=>'1']
        );

        $expected = array(
            ['group_id'=>'1', 'signature'=>'Programming basics', 'Students'=>array(
                ['student_id'=>'1', 'name'=>'John Doe', 'group_id'=>'1'],
                ['student_id'=>'4', 'name'=>'Judy Doe', 'group_id'=>'1']
            )],
            ['group_id'=>'2', 'signature'=>'Computing maths', 'Students'=>array(
                ['student_id'=>'2', 'name'=>'Jane Doe', 'group_id'=>'2'],
                ['student_id'=>'3', 'name'=>'James Doe', 'group_id'=>'2']
            )]
        );

        $groups_students = Arrays::nest($groups, $students, 'Students', function($group, $student) {
            return $group['group_id'] == $student['group_id'];
        });

        $this->assertEquals($expected, $groups_students);

        $groups_obj = json_decode(json_encode($groups));
        $students_obj = json_decode(json_encode($students));
        $expected_obj = json_decode(json_encode($expected));

        $groups_students_obj = Arrays::nest($groups_obj, $students_obj, 'Students', function($group, $student) {
            return $group->group_id == $student->group_id;
        });

        $this->assertEquals($expected_obj, $groups_students_obj);
    }

    public function testIndex()
    {
        $data = array(
            0 => '1',
            1 => '1',
            3 => '2',
            4 => '3',
            5 => '2',
            9 => '1'
        );

        $expected = array(
            '1' => [0, 1, 9],
            '2' => [3, 5],
            '3' => [4]
        );

        $actual = Arrays::index($data);

        $this->assertEquals($expected, $actual);
    }

    public function testReplaceKeys()
    {
        $array = array(
            0 => '0',
            1 => '1',
            '2' => '2',
            'three' => '3',
            'four' => 'four'
        );

        $replaces = array(
            0 => 0,
            '2' => 2,
            'three' => 3,
            'four' => 4
        );

        $expected = array(
            0 => '0',
            2 => '2',
            3 => '3',
            4 => 'four'
        );

        $actual = Arrays::replaceKeys($array, array_keys($replaces), array_values($replaces));
        $this->assertEquals($expected, $actual);
        
        $actual = Arrays::replaceKeys($array, $replaces);
        $this->assertEquals($expected, $actual);
    }
}
