<?php

namespace MAIO;

use Illuminate\Support\Arr;
use MAIO\Attributes\Key;
use MAIO\Attributes\StaticCall;
use MAIO\Exceptions\KeyInArrayNotFoundException;
use ReflectionClass;
use ReflectionProperty;

class MergeArrayIntoObject
{

    private function handleProperty(object $target, ReflectionClass $targetReflection, ReflectionProperty $property, array $data): void
    {
        $key = $property->getName();

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $firstArg = $attribute->getArguments()[0];

            if (empty($firstArg)) {
                continue;
            }

            $key = $firstArg;
        }

        foreach ($property->getAttributes(StaticCall::class) as $attribute) {
            $methodName = $attribute->getArguments()[0];

            if (empty($methodName)) {
                continue;
            }

            if (!$targetReflection->hasMethod($methodName)) {
                continue;
            }

            $method = $targetReflection->getMethod($methodName);
            $value = $method->invoke($target, Arr::get($data, $key));
        }

        if (!Arr::has($data, $key)) {
            throw new KeyInArrayNotFoundException($key);
        }

        if (!isset($value)) {
            $value = Arr::get($data, $key);
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
            $this->handleProperty($target, $targetReflection, $property, $data);
        }

        return $target;
    }
}