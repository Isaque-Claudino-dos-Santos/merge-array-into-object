<?php

namespace MAIO;

use Illuminate\Support\Arr;
use MAIO\Attributes\Call;
use MAIO\Attributes\Key;
use MAIO\Exceptions\KeyInArrayNotFoundException;
use MAIO\Exceptions\MethodNotExistsException;
use ReflectionClass;
use ReflectionProperty;

class MergeArrayIntoObject
{

    private function handleProperty(object $target, ReflectionProperty $property, array $data): void
    {
        $key = $property->getName();
        $hasDefaultValue = $property->hasDefaultValue();
        $defaultValue = $hasDefaultValue ? $property->getDefaultValue() : null;
        $value = Arr::get($data, $key, $defaultValue);

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $argKey = $attribute->getArguments()[0];

            if (empty($argKey)) {
                continue;
            }

            if (Arr::has($data, $argKey)) {
                $key = $argKey;
                $value = Arr::get($data, $key, $defaultValue);
            }
        }

        foreach ($property->getAttributes(Call::class) as $attribute) {
            $objectOrClassOrFunction = $attribute->getArguments()[0];
            $methodName = $attribute->getArguments()[1];

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

        $dontIsAvailableToSetValueInProperty = !$hasDefaultValue && !Arr::has($data, $key);

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
