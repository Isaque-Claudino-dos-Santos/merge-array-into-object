<?php

namespace MAIO;

use Illuminate\Support\Arr;
use MAIO\Attributes\Key;
use MAIO\Attributes\StaticCall;
use ReflectionObject;
use ReflectionProperty;

class MergeArrayIntoObject
{

    private function handleProperty(object $target, ReflectionProperty $property, array $data): void
    {
        $key = $property->getName();
        $value = Arr::get($data, $property->getName());

        foreach ($property->getAttributes(Key::class) as $attribute) {
            $attribute = $attribute->newInstance();
            $key = $attribute->key;
        }

        foreach ($property->getAttributes(StaticCall::class) as $attribute) {
            if ($property->getType()->isBuiltin()) {
                break;
            }
            $attribute = $attribute->newInstance();
            $value = call_user_func($property->getType() . '::' . $attribute->methodName, Arr::get($data, $key));
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
        $targetReflection = new ReflectionObject($target);

        if (empty($data)) {
            return $target;
        }

        foreach ($targetReflection->getProperties() as $property) {
            $this->handleProperty($target, $property, $data);
        }


        return $target;
    }
}