webnium/json-pointer
====================
* master: [![Build Status](https://travis-ci.org/webnium/php-json-pointer.png?branch=master)](https://travis-ci.org/webnium/php-json-pointer)


Usage
------

### ArrayAccessor
```php
<?php

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
```

Output:
```
/foo/bar: 1
/fizz/buzz: 2
/list/0: 'item0'
/list/2: 'item2'
root: array (
  'foo' => 
  array (
    'bar' => 1,
  ),
  'list' => 
  array (
    0 => 'item0',
    1 => 'item1',
    2 => 'item2',
  ),
  'fizz' => 
  array (
    'buzz' => 2,
  ),
)
```

### Parser

```php
<?php

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
```

Output:
```
/foo/bar: array (
  0 => 'foo',
  1 => 'bar',
)
/: array (
  0 => '',
)
: array (
)
/foo/~01/~1: array (
  0 => 'foo',
  1 => '~1',
  2 => '/',
)
/~a/aaa: thrown 'Webnium\JsonPointer\Exception\SyntaxError' with message 'unknown escape sequence "~a" detected.'
foo: thrown 'Webnium\JsonPointer\Exception\SyntaxError' with message 'pointer start with "f", "/" expected.'
```

LICENSE
-------
This library destributed under MIT license.
See LICENSE file for more infomation.
