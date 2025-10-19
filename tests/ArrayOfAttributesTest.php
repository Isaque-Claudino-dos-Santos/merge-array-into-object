<?php

namespace ISQ\MAIO\Tests\MergeArrayIntoObject;

use ISQ\MAIO\Attributes\ArrayOf;
use ISQ\MAIO\Attributes\Call;
use ISQ\MAIO\Attributes\Key;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class User
{
	public string $name;

	public static function getName(User $user)
	{
		return $user->name;
	}
}

/**
 * @internal
 *
 * @coversNothing
 */
class ArrayOfAttributesTest extends TestCase
{
	#[Test]
	#[Ticket('#22')]
	public function itShouldTransformArrayValueInObjectPassedOnDefineAttributeArrayOfSuccessfully()
	{
		$target = new class {
			/** @var array<User> */
			#[ArrayOf(User::class)]
			public array $users;
		};

		$data = [
			'users' => [
				['name' => 'Mick'],
				['name' => 'Michael'],
				['name' => 'Isaque'],
				['name' => 'Pablo'],
			],
		];

		$result = (new MergeArrayIntoObject())->merge($target, $data);

		$this->assertEquals('Mick', $result->users[0]->name);
		$this->assertEquals('Michael', $result->users[1]->name);
		$this->assertEquals('Isaque', $result->users[2]->name);
		$this->assertEquals('Pablo', $result->users[3]->name);
	}

	#[Test]
	#[Ticket('#22')]
	public function itShouldReturnExpectedArrayPropertyToUseArrayOfAttributeOnUseArrayOfInPropertyWithError()
	{
		$this->expectException(\Exception::class);

		$target = new class {
			/** @var array<User> */
			#[ArrayOf(User::class)]
			public string $users;
		};

		$data = [
			'users' => [
				['name' => 'Mick'],
				['name' => 'Michael'],
				['name' => 'Isaque'],
				['name' => 'Pablo'],
			],
		];

		(new MergeArrayIntoObject())->merge($target, $data);
	}

	#[Test]
	#[Ticket('#22')]
	public function itShouldReturnExpectedArrayDataReceivedOnUseArrayOfInPropertyWithError()
	{
		$this->expectException(\Exception::class);

		$target = new class {
			/** @var array<User> */
			#[ArrayOf(User::class)]
			public array $users;
		};

		$data = [
			'users' => 'hello error',
		];

		(new MergeArrayIntoObject())->merge($target, $data);
	}

	#[Test]
	#[Ticket('#31')]
	public function itShouldTransformArrayValueInObjectPassedOnDefineAttributeArrayOfWithCallAttributeSuccessfully()
	{
		$target = new class {
			/** @var array<string> */
			#[Key('users'), ArrayOf(User::class), Call(User::class, 'getName')]
			public array $names;
		};

		$data = [
			'users' => [
				['name' => 'Mick'],
				['name' => 'Michael'],
				['name' => 'Isaque'],
				['name' => 'Pablo'],
			],
		];

		$result = (new MergeArrayIntoObject())->merge($target, $data);
		$this->assertEquals($result->names, [
			'Mick',
			'Michael',
			'Isaque',
			'Pablo',
		]);
	}
}
