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
    /** @var ArrayAccessor */
    private $accessor;

    /** @var ParserInterface */
    private $parser;

    /**
     * setup
     */
    public function setup()
    {
        $this->parser = Phake::mock('Webnium\JsonPointer\ParserInterface');
        $this->accessor = new ArrayAccessor($this->parser);
    }

    /**
     * @test
     *
     * @param array $array    target array
     * @param array $pointer  a parsed JSON Pointer
     * @param mixed $expected expected value
     *
     * @dataProvider provideDataForCanGetPointedValueIfExistsTest
     */
    public function canGetPointedValueIfExists($array, $parsedPointer, $expected)
    {
        $pointer = '/foo/bar';
        Phake::when($this->parser)->parse($pointer)->thenReturn($parsedPointer);
        $this->assertSame($expected, $this->accessor->get($pointer, $array));

        Phake::verify($this->parser)->parse($pointer);
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
            'root' => [$array, [], $array],
            'level 1' => [$array, ['foo'], 'a string'],
            'level 2' => [$array, ['bar', 'buzz'], 3],
            'list item' => [$array, ['list', '0'], 'item1'],
            'empty string key' => [$array, [''], 'value of empty string key'],
            'escaped' => [$array, ['~/'], 'value of escaped key'],
        ];
    }

    /**
     * JSON Pointer Syntax Error
     *
     * @test
     * @expectedException Webnium\JsonPointer\Exception\SyntaxError
     */
    public function throwAnSytaxErrorExceptionWhenSuppliedPointerHasSyntaxError()
    {
        Phake::when($this->parser)->parse(Phake::anyParameters())->thenThrow(new Exception\SyntaxError);
        $this->accessor->get('foo', ['foo' => 1]);
    }

    /**
     * References none existent value
     *
     * @test
     * @expectedException Webnium\JsonPointer\Exception\NoneExistentValue
     */
    public function throwANoneExistentValueExceptionWhenPointerReferencesNoneExistentValue()
    {
        Phake::when($this->parser)->parse('/bar/0')->thenReturn(['bar', '0']);

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
        Phake::when($this->parser)->parse('/bar/0')->thenReturn(['bar', '0']);

        $this->accessor->get('/bar/0', ['bar' => 'hoge']);
    }

    /**
     * Set value
     *
     * @test
     * @dataProvider provideDataForCanSetPointedValue
     */
    public function canSetPointedValue($parsedPointer, $baseArray)
    {
        $value = 'new value';
        $array = $baseArray;
        $pointer = '/foo/bar';

        Phake::when($this->parser)->parse($pointer)->thenReturn($parsedPointer);

        $this->accessor->set($pointer, $array, $value);

        Phake::verify($this->parser)->parse($pointer);

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
            'overwirte' => [['foo', 'bar'], $array], 
            'new key' => [['buzz'], $array],
            'deep new key' => [['buzz', 'aaa'], $array],
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

        Phake::when($this->parser)->parse('/list/-')->thenReturn(['list', '-']);
        Phake::when($this->parser)->parse('/list/2')->thenReturn(['list', '2']);

        $this->accessor->set('/list/-', $array, $value);

        $this->assertSame($value, $this->accessor->get('/list/2', $array));
    }
}
