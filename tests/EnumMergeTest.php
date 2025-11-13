<?php

use ISQ\MAIO\Dev\_ExampleClass;
use ISQ\MAIO\Dev\_ExampleEnum;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EnumMergeTest extends TestCase
{
	#[Test]
	#[Ticket('#46')]
	public function it_should_merge_enum_on_call_merge_method_successfully()
	{
		$data = [
			'enum1' => _ExampleEnum::TEST->value,
			'enum2' => _ExampleEnum::TEST2,
			'enums' => [1, 2, 2, 1, _ExampleEnum::TEST2],
		];

		$targetMerged = (new MergeArrayIntoObject())->merge(new _ExampleClass(), $data);

		$this->assertInstanceOf(BackedEnum::class, $targetMerged->enum1);
		$this->assertInstanceOf(BackedEnum::class, $targetMerged->enum2);
		$this->assertNull($targetMerged->enum3);
		$this->assertEquals(
			[
				_ExampleEnum::TEST,
				_ExampleEnum::TEST2,
				_ExampleEnum::TEST2,
				_ExampleEnum::TEST,
				_ExampleEnum::TEST2,
			],
			$targetMerged->enums
		);
	}
}
