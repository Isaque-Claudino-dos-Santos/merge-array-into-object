<?php

namespace Tests;

use ISQ\MAIO\Dev\_ExampleCreateCompany;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TypeCheckOnSetPropertyTest extends TestCase
{
	#[Test]
	#[Ticket('#44')]
	public function it_should_dont_throw_erro_on_merge_property_with_type_class()
	{
		$data = [
			'name' => 'My Corp',
			'user' => [
				'id' => 1,
				'name' => 'John',
				'age' => 43,
			],
		];

		$targetResolved = new MergeArrayIntoObject()->merge(new _ExampleCreateCompany(), $data);

		$this->assertEquals('My Corp', $targetResolved->name);
		$this->assertEquals(1, $targetResolved->user->id);
		$this->assertEquals('John', $targetResolved->user->name);
		$this->assertEquals(43, $targetResolved->user->age);
	}
}
