<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$array = [
'foo' => ['bar' => 1],
    'list' => ['item0', 'item1'],
    ];

$accessor = new Webnium\JsonPointer\ArrayAccessor(new Webnium\JsonPointer\Parser);

$accessor->set('/fizz/buzz', $array, 2);
$accessor->set('/list/-', $array, 'item2');

echo '/foo/bar: ', var_export($accessor->get('/foo/bar', $array), true), PHP_EOL;
echo '/fizz/buzz: ', var_export($accessor->get('/fizz/buzz', $array), true), PHP_EOL;
echo '/list/0: ', var_export($accessor->get('/list/0', $array), true), PHP_EOL;
echo '/list/2: ', var_export($accessor->get('/list/2', $array), true), PHP_EOL;
echo 'root: ', var_export($accessor->get('', $array), true), PHP_EOL;
