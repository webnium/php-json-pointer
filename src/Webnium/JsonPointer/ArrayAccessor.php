<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer;

/**
 * Array element accessor using JSON Pointer
 *
 */
class ArrayAccessor
{
    /**
     * constructor
     *
     * @param Paser $parser JSON Pointer Parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * get pointed value
     *
     * @param string $pointer JSON Pointer string
     * @param array  $array   target array
     *
     * @throws Exception\SyntaxError
     * @throws Exception\NoneExistentValue
     */
    public function get($pointer, $array)
    {
        $pointerArray = $this->parser->parse($pointer);

        $current = $array;
        foreach ($pointerArray as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                throw new Exception\NoneExistentValue('references none existent value.');
            }

            $current = $current[$key];
        }

        return $current;
    }

    /**
     * set pointed value
     *
     * @param string $pointer JSON Pointer string
     * @param array  &$array   target array
     * @param mixed  $value   value to set
     *
     * @throws Exception\SyntaxError
     * @throws Exception\NoneExistentValue
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.EvalExpression)
     */
    public function set($pointer, &$array, $value)
    {
        $pointerArray = $this->parser->parse($pointer);

        $code = '$array';

        foreach ($pointerArray as $key) {
            if ($key === '-') {
                $code .= '[]';
                continue;
            }
            $code .= sprintf('[%s]', var_export($key, true));
        }

        $code .= '=$value;';

        // XXX: we shouldn't use eval expression
        // However without eval expression this operation will make unexpected reference.
        // If anyone know better way, please inform me :)
        eval($code);
    }
}
