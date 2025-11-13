<?php

namespace ISQ\MAIO\Attributes\Processors;

use ISQ\MAIO\MergeArrayIntoObject;

class ArrayOfProcessor extends Processor
{
	private mixed $value = null;

	public function __construct(private readonly mixed $data)
	{
	}

	public function process(\ReflectionAttribute $attribute): void
	{
		$className = $attribute->getArguments()[0] ?? null;

		if (! is_array($this->data) || ! array_is_list($this->data)) {
			return;
		}

		if (! class_exists($className)) {
			return;
		}

		if (enum_is_backend($className)) {
			$enumClass = $className;
		}

		$valueMapperHandle = function ($item) use ($className, $enumClass) {
			if (isset($enumClass) && $item instanceof $enumClass) {
				return $item;
			}

			if (isset($enumClass)) {
				return $enumClass::tryFrom($item);
			}

			return MergeArrayIntoObject::getInstance()->merge(new $className(), $item);
		};

		$this->value = array_map($valueMapperHandle, $this->data);
	}

	public function getValue(): mixed
	{
		return $this->value;
	}
}
