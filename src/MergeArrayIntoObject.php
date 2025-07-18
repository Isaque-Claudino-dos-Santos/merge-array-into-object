<?php

namespace MAIO;

use MAIO\Attributes\Call;
use MAIO\Attributes\Key;
use MAIO\Exceptions\KeyInArrayNotFoundException;
use MAIO\Exceptions\MethodNotExistsException;
use ReflectionClass;
use ReflectionProperty;

class MergeArrayIntoObject
{
    private function arrayHas(array $data, string|int|null $key): bool
    {
        if ($key === null) {
            return false;
        }

        if (is_int($key)) {
            return key_exists($key, $data);
        }

        $paths = explode('.', $key);

        foreach ($paths as $path) {
            if (!key_exists($path, $data)) {
                return false;
            }

            $data = $data[$path];
        }

        return true;
    }

    private function arrayDontHas(array $data, string|int|null $key): bool
    {
        return !$this->arrayHas($data, $key);
    }

    private function arrayGet(array $data, string|int|null $key, mixed $defaultValue = null): mixed
    {
        if ($key === null) {
            return $defaultValue;
        }

        if (is_int($key)) {
            return key_exists($key, $data) ? $data[$key] : $defaultValue;
        }

        $paths = explode('.', $key);

        foreach ($paths as $path) {
            if (!key_exists($path, $data)) {
                return $defaultValue;
            }

            $data = $data[$path];
        }

        return $data;
    }

    private function handleProperty(object $target, ReflectionProperty $property, array $data): void
    {
        $key = $property->getName();
        $hasDefaultValue = $property->hasDefaultValue();
        $defaultValue = $hasDefaultValue ? $property->getDefaultValue() : null;
        $value = $this->arrayGet($data, $key, $defaultValue);

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $key = $attribute->getArguments()[0] ?? null;

            if ($this->arrayHas($data, $key)) {
                $value = $this->arrayGet($data, $key, $defaultValue);
            }
        }

        foreach ($property->getAttributes(Call::class) as $attribute) {
            $objectOrClassOrFunction = $attribute->getArguments()[0];
            $methodName = $attribute->getArguments()[1] ?? null;

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

        $dontIsAvailableToSetValueInProperty = !$hasDefaultValue && $this->arrayDontHas($data, $key);

        if ($dontIsAvailableToSetValueInProperty) {
            throw new KeyInArrayNotFoundException($key);
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
