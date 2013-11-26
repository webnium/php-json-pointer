<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer;

use \Phake;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Basic test case for Parser class.
 */
class ParserTest extends TestCase
{
    /** @ parser */
    private $parser;

    /**
     * setup
     */
    public function setup()
    {
        $this->parser = new Parser;
    }

    /**
     * Parse valid pointer
     *
     * @test
     * @dataProvider provideValidJsonPinter
     */
    public function canParseValidJsonPonter($pointer, $expected)
    {
        $this->assertSame($expected, $this->parser->parse($pointer));
    }

    /**
     * Data Provider
     * 
     * @return array
     */
    public function provideValidJsonPinter()
    {
        return [
            'root' => ['', []],
            'level 1' => ['/foo', ['foo']],
            'level 2' => ['/bar/buzz', ['bar', 'buzz']],
            'list item' => ['/list/0', ['list', '0']],
            'empty string key' => ['/', ['']],
            'escaped' => ['/~01~1', ['~1/']],
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
        $this->parser->parse($pointer);
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
}
