<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer;

/**
 * JSON Pointer parser
 *
 */
class Parser
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
    public function parse($pointer)
    {
        if ($pointer === '') {
            return [];
        }

        if ($pointer[0] !== '/') {
            throw new Exception\SyntaxError(sprintf('pointer start with "%s", "/" expected.', $pointer[0]));
        }

        $pointerArray = @array_map(function ($referenceToken) {
            return preg_replace_callback('/~./', function ($matches) {
                $escaped = $matches[0];

                if ($escaped === '~0') {
                    return '~';
                }
                if ($escaped === '~1') {
                    return '/';
                }

                throw new Exception\SyntaxError(sprintf('unknown escape sequence "%s" detected.', $escaped));
            }, $referenceToken);
        }, explode('/', $pointer));

        array_shift($pointerArray);

        return $pointerArray;
    }
}
