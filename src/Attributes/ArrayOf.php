<?php

namespace ISQ\MAIO\Attributes;

/**
 * @template T
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ArrayOf
{
	/**
	 * @param class-string<T> $target
	 */
	public function __construct(public string $target)
	{
	}
}
