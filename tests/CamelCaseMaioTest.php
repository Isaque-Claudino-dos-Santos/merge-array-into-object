<?php

namespace Tests;

use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CamelCaseMaioTest extends TestCase
{
	#[Test]
	#[Ticket('#21')]
	public function itShouldResolveCamelCaseArrayKeyInAttributeOfClassSuccessfully()
	{
		MergeArrayIntoObject::$checkCamelCase = true;

		$target = new class {
			public string $first_name;
		};

		$data = [
			'firstName' => 'John',
		];

		$resolved = (new MergeArrayIntoObject())->merge($target, $data);

		$this->assertEquals('John', $resolved->first_name);
	}
}
