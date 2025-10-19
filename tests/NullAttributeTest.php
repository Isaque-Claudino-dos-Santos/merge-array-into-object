<?php

namespace ISQ\MAIO\Tests\MergeArrayIntoObject;

use ISQ\MAIO\Dev\_ExampleNullAttribute;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NullAttributeTest extends TestCase
{
	#[Test]
	#[Ticket('#34')]
	public function it_should_define_attribute_with_null_on_not_initialize_attribute_successfully()
	{
		$maio = MergeArrayIntoObject::getInstance();
		$target = new _ExampleNullAttribute();
		$resolvedTarget = $maio->merge($target, ['age' => 12]);

		$this->assertNull($resolvedTarget->name);
		$this->assertEquals(12, $resolvedTarget->age);
	}
}
