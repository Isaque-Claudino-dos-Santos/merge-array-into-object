<?php

namespace MAIO;

use Illuminate\Support\Arr;
use MAIO\Attributes\Call;
use MAIO\Attributes\Key;
use MAIO\Exceptions\MethodNotExistsException;
use ReflectionClass;
use ReflectionProperty;

class MergeArrayIntoObject
{

    private function handleProperty(object $target, ReflectionProperty $property, array $data): void
    {
        $key = $property->getName();
        $defaultValue = $property->hasDefaultValue() ? $property->getDefaultValue() : null;
        $value = Arr::get($data, $key, $defaultValue);

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $firstArg = $attribute->getArguments()[0];

            if (empty($firstArg)) {
                continue;
            }

            $key = $firstArg;
            $value = Arr::get($data, $key, $defaultValue);
        }

        foreach ($property->getAttributes(Call::class) as $attribute) {
            $objectOrClass = $attribute->getArguments()[0];
            $methodName = $attribute->getArguments()[1];

            $isClass = class_exists($objectOrClass);
            $isFunction = function_exists($objectOrClass);
            $isObject = is_object($objectOrClass);

            if ($isClass || $isObject) {
                $reflection = new ReflectionClass($objectOrClass);

                if (!$reflection->hasMethod($methodName)) {
                    throw new MethodNotExistsException("Method static $methodName not found in $objectOrClass");
                }

                $method = $reflection->getMethod($methodName);
                $value = $method->invoke($target, $value);
            }

            if ($isFunction) {
                $value = $objectOrClass($value);
            }
        }

        $property->setValue($target, $value);
    }

    /**
     * @template T
     * 
     * @param T $target
     * @param array|null $data
     * @return T
     */
    public function merge(object $target, array|null $data): object
    {
        if (empty($data)) {
            return $target;
        }
        $targetReflection = new ReflectionClass($target);

        foreach ($targetReflection->getProperties() as $property) {
            $this->handleProperty($target, $property, $data);
        }

        return $target;
    }
}