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

		$this->value = array_map(
			fn ($item) => MergeArrayIntoObject::getInstance()->merge(new $className(), $item),
			$this->data
		);
	}

	public function getValue(): mixed
	{
		return $this->value;
	}
}
