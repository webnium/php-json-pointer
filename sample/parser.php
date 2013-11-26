<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Webnium\JsonPointer\Parser;
use Webnium\JsonPointer\Exception\ExceptionInterface as JsonPointerException;

$parser = new Parser;

$pointers = [
    '/foo/bar',
    '/',
    '',
    '/foo/~01/~1',
    '/~a/aaa',
    'foo'
];

foreach ($pointers as $pointer) {
    try {
        $parsed = $parser->parse($pointer);
        echo "$pointer: ", var_export($parsed, true), PHP_EOL;
    } catch (JsonPointerException $e) {
        echo "$pointer: ", 'thrown \'' . get_class($e) . '\' with message \'' . $e->getMessage() . '\'', PHP_EOL;
    }
}
