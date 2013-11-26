<?php
/**
 * This file is part of webnium/json-pointer.
 */

namespace Webnium\JsonPointer\Exception;

/**
 * None existent value exception.
 *
 * This exception will be thrown when a pointer references none existent value.
 */
class NoneExistentValue extends \RuntimeException implements ExceptionInterface
{
}
