<?php

namespace ISQ\MAIO\Tests\MergeArrayIntoObject;

use ISQ\MAIO\Attributes\Call;
use ISQ\MAIO\Attributes\Key;
use ISQ\MAIO\Exceptions\KeyInArrayNotFoundException;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MergeArrayIntoObjectTest extends TestCase
{
	#[Test]
	#[Ticket('#1'), Ticket('#16')]
	public function itShouldCallStaticMethodOnStaticCallIsPresentInPropertySuccessfully()
	{
		$object = new class {
			#[Key('user.full_name')]
			#[Call('strtoupper')]
			public string $name;
		};

		$array = [
			'user' => [
				'full_name' => 'john doe',
			],
		];

		$maio = new MergeArrayIntoObject();
		$mergedObject = $maio->merge($object, $array);
		$this->assertEquals('JOHN DOE', $mergedObject->name);
	}

	#[Test]
	#[Ticket('#5')]
	public function itShouldReturnDefaultValueOnNotFoundKeyInArraySuccessfully()
	{
		$object = new class {
			public string $name = 'Marry Jane';
		};

		$array = ['full_name' => 'john doe'];

		$maio = new MergeArrayIntoObject();
		$mergedObject = $maio->merge($object, $array);

		$this->assertEquals('Marry Jane', $mergedObject->name);
	}

	#[Test]
	#[Ticket('#9')]
	public function itShouldThrowKeyInArrayNotFoundOnSetDefineKeyNotExistentInClassAndWithoutDefaultValueWithError()
	{
		$this->expectException(KeyInArrayNotFoundException::class);

		$object = new class {
			public string $name;
		};

		$array = ['age' => 12];

		MergeArrayIntoObject::getInstance()->merge($object, $array);
	}
}
