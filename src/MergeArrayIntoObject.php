<?php

namespace ISQ\MAIO;

use ISQ\MAIO\Attributes\ArrayOf;
use ISQ\MAIO\Attributes\Call;
use ISQ\MAIO\Attributes\Key;
use ISQ\MAIO\Attributes\Processors\ArrayOfProcessor;
use ISQ\MAIO\Attributes\Processors\KeyProcessor;
use ISQ\MAIO\Exceptions\InvalidTypeToSetException;
use ISQ\MAIO\Exceptions\KeyInArrayNotFoundException;
use ISQ\MAIO\Traits\ResolveCamelCaseTrait;
use ISQ\MAIO\Traits\ResolveSnakeCaseTrait;

class MergeArrayIntoObject
{
	use ResolveSnakeCaseTrait;
	use ResolveCamelCaseTrait;

	private static ?MergeArrayIntoObject $instance = null;

	public static bool $checkSnakeCase = false;
	public static bool $checkCamelCase = false;

	public static function getInstance(): MergeArrayIntoObject
	{
		if (null == self::$instance) {
			return new MergeArrayIntoObject();
		}

		return self::$instance;
	}

	private function handleProperty(object $target, \ReflectionProperty $property, array $data): void
	{
		$key = $property->getName();
		$keyExistsInData = array_has_key($data, $key);
		$propertyType = $property->getType();
		$propertyTypeName = $propertyType->__toString();
		$propertyAllowsNull = $propertyType->allowsNull();
		$hasDefaultValue = $property->hasDefaultValue() || $propertyAllowsNull;
		$defaultValue = (! $keyExistsInData && $hasDefaultValue) ? $property->getDefaultValue() : null;
		$value = array_get($data, $key, $defaultValue);

		if (! $keyExistsInData && self::$checkSnakeCase) {
			$this->resolveSnakeCase($key, $value, $data);
		}

		if (! $keyExistsInData && self::$checkCamelCase) {
			$this->resolveCamelCase($key, $value, $data);
		}

		foreach ($property->getAttributes(Key::class) as $attribute) {
			$processor = new KeyProcessor($data);
			$processor->process($attribute);
			$keyExistsInData = $processor->keyExists();
			$key = $processor->getKey() ?? $key;
			$value = $processor->getValue() ?? $defaultValue;
		}

		foreach ($property->getAttributes(ArrayOf::class) as $attribute) {
			$processor = new ArrayOfProcessor($value);
			$processor->process($attribute);
			$value = $processor->getValue() ?? $defaultValue;
		}

		if (enum_is_backend($propertyTypeName) && (is_int($value) || is_string($value))) {
			$value = $propertyTypeName::tryFrom($value);
		}

		foreach ($property->getAttributes(Call::class) as $attribute) {
			$attribute->newInstance()->process($target, $value);
		}

		$dontIsAvailableToSetValueInProperty = ! $hasDefaultValue && ! array_has_key($data, $key);

		if ($dontIsAvailableToSetValueInProperty) {
			throw new KeyInArrayNotFoundException($key);
		}

		try {
			$property->setValue($target, $value);
		} catch (\TypeError $e) {
			throw new InvalidTypeToSetException($key);
		}
	}

	/**
	 * @template T
	 *
	 * @param T $target
	 *
	 * @return T
	 */
	public function merge(mixed $target, ?array $data): object
	{
		$targetReflection = new \ReflectionClass($target);

		foreach ($targetReflection->getProperties() as $property) {
			$this->handleProperty($target, $property, $data);
		}

		return $target;
	}
}
