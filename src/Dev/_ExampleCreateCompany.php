<?php

namespace ISQ\MAIO\Dev;

use ISQ\MAIO\Attributes\Call;

class _ExampleCreateCompany
{
	public readonly string $name;
	#[Call(_ExampleUser::class, 'merge')]
	public readonly _ExampleUser $user;
}
