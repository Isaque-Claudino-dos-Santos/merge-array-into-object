<?php

namespace ISQ\MAIO;

use Exception;
use ISQ\MAIO\Attributes\ArrayOf;
use ISQ\MAIO\Attributes\Call;
use ISQ\MAIO\Attributes\Key;
use ISQ\MAIO\Exceptions\KeyInArrayNotFoundException;
use ISQ\MAIO\Exceptions\MethodNotExistsException;
use ReflectionClass;
use ReflectionProperty;

class MergeArrayIntoObject
{
    public static bool $checkSnakeCase = false;
    public static bool $checkCamelCase = false;

    private function toCamelCase(string $value, ?bool $toTitle = false): string
    {
        $value = preg_split('/_/', $value, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $value = array_map(fn(string $word) => ucfirst($word), $value);
        $value = implode('', $value);

        if (!$toTitle) {
            $value = lcfirst($value);
        }

        return $value;
    }

    private function toSnakeCase(string $value, bool $toLower = true): string
    {
        $value = preg_split('/([A-Z][a-z]*)/', $value, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $value = implode('_', $value);

        if ($toLower) {
            $value = strtolower($value);
        }

        return $value;
    }

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
        $keyExistsInData = $this->arrayHas($data, $key);
        $hasDefaultValue = $property->hasDefaultValue();
        $defaultValue = (!$keyExistsInData && $hasDefaultValue) ? $property->getDefaultValue() : null;
        $value = $this->arrayGet($data, $key, $defaultValue);

        // Process snake case in key
        if (!$keyExistsInData && self::$checkSnakeCase) {
            $keyInSnakeCase = $this->toSnakeCase($key);
            $keyExistsInData = $this->arrayHas($data, $keyInSnakeCase);

            if ($keyExistsInData) {
                $key = $keyInSnakeCase;
                $value = $this->arrayGet($data, $key);
            }
        }

        // Process camel case in key
        if (!$keyExistsInData && self::$checkCamelCase) {
            $keyInCamelCase = $this->toCamelCase($key);
            $keyExistsInData = $this->arrayHas($data, $keyInCamelCase);

            if ($keyExistsInData) {
                $key = $keyInCamelCase;
                $value = $this->arrayGet($data, $key);
            }
        }

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $key = $attribute->getArguments()[0] ?? null;

            if ($this->arrayHas($data, $key)) {
                $keyExistsInData = true;
                $value = $this->arrayGet($data, $key, $defaultValue);
            }
        }

        foreach ($property->getAttributes(ArrayOf::class) as $attribute) {
            $className = $attribute->getArguments()[0] ?? null;

            if ($property->getType()->__tostring() !== 'array') {
                throw new Exception("The property {$key} should is array to use ArrayOf attribute");
            }

            if (!is_array($value)) {
                throw new Exception("Received value on {$key} should is array");
            }

            $value = array_map(fn($item): object => $this->merge(new $className, $item), $value);
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
