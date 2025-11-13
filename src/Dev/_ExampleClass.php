<?php

namespace ISQ\MAIO\Dev;

use ISQ\MAIO\Attributes\ArrayOf;

readonly class _ExampleClass
{
	public _ExampleEnum $enum1;
	public _ExampleEnum $enum2;
	public ?_ExampleEnum $enum3;
	#[ArrayOf(_ExampleEnum::class)]
	public array $enums;
}
