<?php

namespace ISQ\MAIO\Attributes;

use Attribute;
use ISQ\MAIO\Exceptions\MethodNotExistsException;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Call
{
    public function __construct(
        public string|object $staticClassOrObjectOrFunction,
        public string|null $methodName = null,
    ) {
    }

    public function process(object $target, mixed &$value)
    {
        $objectOrClassOrFunction = $this->staticClassOrObjectOrFunction;
        $methodName = $this->methodName;

        $isClass = class_exists($objectOrClassOrFunction);
        $isFunction = function_exists($objectOrClassOrFunction);
        $isObject = is_object($objectOrClassOrFunction);

        if ($isClass || $isObject) {
            $reflection = new ReflectionClass($objectOrClassOrFunction);

            if (!$reflection->hasMethod($methodName)) {
                throw new MethodNotExistsException("Method static $methodName not found in $objectOrClassOrFunction");
            }

            $method = $reflection->getMethod($methodName);
            $value = $method->invoke($target, $value);
        }

        if ($isFunction) {
            $value = $objectOrClassOrFunction($value);
        }

    }
}
