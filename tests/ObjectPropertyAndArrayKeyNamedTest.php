<?php

namespace ISQ\MAIO\Tests\MergeArrayIntoObject;

use ISQ\MAIO\Attributes\Key;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ObjectPropertyAndArrayKeyNamedTest extends TestCase
{
	#[Test]
	#[Ticket('#1')]
	public function itShouldSetValueInObjectWithNamedPropertySuccessfully()
	{
		$object = new class {
			#[Key('property_named_test')]
			public string $property;
		};

		$array = ['property_named_test' => 'John Doe'];

		$maio = new MergeArrayIntoObject();
		$mergedObject = $maio->merge($object, $array);

		$this->assertEquals($array['property_named_test'], $mergedObject->property);
	}
}
