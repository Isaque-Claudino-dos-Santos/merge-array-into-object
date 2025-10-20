<?php

namespace ISQ\MAIO\Dev;

use ISQ\MAIO\MergeArrayIntoObject;

class _ExampleUser
{
	public readonly int $id;
	public readonly string $name;
	public readonly int $age;

	public static function merge($data)
	{
		return MergeArrayIntoObject::getInstance()->merge(new static(), $data);
	}
}
