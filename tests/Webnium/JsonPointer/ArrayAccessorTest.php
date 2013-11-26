<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer;

use \Phake;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Basic test case for ArrayAccessor class.
 */
class ArrayAccessorTest extends TestCase
{
    /** @ ArrayAccessor */
    private $accessor;

    /**
     * setup
     */
    public function setup()
    {
        $this->accessor = new ArrayAccessor(new Parser);
    }

    /**
     * @test
     *
     * @param array  $array    target array
     * @param string $pointer  a JSON Pointer
     * @param mixed  $expected expected value
     *
     * @dataProvider provideDataForCanGetPointedValueIfExistsTest
     */
    public function canGetPointedValueIfExists($array, $pointer, $expected)
    {
        $this->assertSame($expected, $this->accessor->get($pointer, $array));
    }

    /**
     * Data Provider
     * 
     * @return array
     */
    public function provideDataForCanGetPointedValueIfExistsTest()
    {
        $array = [
            'foo' => 'a string',
            'bar' => [
                'buzz' => 3,
            ],
            'list' => [
                'item1',
                'item2',
                'item3',
            ],
            '' => 'value of empty string key',
            '~/' => 'value of escaped key',
        ];

        return [
            'root' => [$array, '', $array],
            'level 1' => [$array, '/foo', 'a string'],
            'level 2' => [$array, '/bar/buzz', 3],
            'list item' => [$array, '/list/0', 'item1'],
            'empty string key' => [$array, '/', 'value of empty string key'],
            'escaped' => [$array, '/~0~1', 'value of escaped key'],
        ];
    }

    /**
     * JSON Pointer Syntax Error
     *
     * @test
     * @expectedException Webnium\JsonPointer\Exception\SyntaxError
     * @dataProvider provideInvalidPointer
     */
    public function throwAnSytaxErrorExceptionWhenSuppliedPointerHasSyntaxError($pointer)
    {
        $this->accessor->get($pointer, ['foo' => 1]);
    }

    /**
     * provides invalid pointer
     *
     * @return array
     */
    public function provideInvalidPointer()
    {
        return [
            'start without "/"' => ['foo'],
            'unknown escape' => ['/~3'],
        ];
    }

    /**
     * References none existent value
     *
     * @test
     * @expectedException Webnium\JsonPointer\Exception\NoneExistentValue
     */
    public function throwANoneExistentValueExceptionWhenPointerReferencesNoneExistentValue()
    {
        $this->accessor->get('/bar/0', []);
    }

    /**
     * References child of none array
     *
     * @test
     * @expectedException Webnium\JsonPointer\Exception\NoneExistentValue
     */
    public function throwANoneExistentValueExceptionWhenPointerReferencesChildOfNoneArrayValue()
    {
        $this->accessor->get('/bar/0', ['bar' => 'hoge']);
    }

    /**
     * Set value
     *
     * @test
     * @dataProvider provideDataForCanSetPointedValue
     */
    public function canSetPointedValue($pointer, $baseArray)
    {
        $value = 'new value';
        $array = $baseArray;

        $this->accessor->set($pointer, $array, $value);

        $this->assertSame($value, $this->accessor->get($pointer, $array));
    }

    /**
     * Data Provider
     *
     * @return array
     */
    public function provideDataForCanSetPointedValue()
    {
        $array = ['foo' => ['bar' => 12], 'list' => ['item1']];

        return [
            'overwirte' => ['/foo/bar', $array], 
            'new key' => ['/buzz', $array],
            'deep new key' => ['/buzz/aaa', $array],
        ];
    }

    /**
     * Append to list
     *
     * @test
     */
    public function canAppendValueToList()
    {
        $array = ['list' => ['item1', 'item2']];
        $value = 'new value';

        $this->accessor->set('/list/-', $array, $value);

        $this->assertSame($value, $this->accessor->get('/list/2', $array));
    }
}
