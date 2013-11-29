<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer;

/**
 * Interface of JSON Pointer parser
 *
 */
interface ParserInterface
{
    /**
     * parse json pointer
     *
     * @param string $pointer JSON Pointer to parse
     *
     * @return string[]
     * @throws Exception\SyntaxError
     * @throws Exception\NoneExistentValue
     */
    public function parse($pointer);
}
