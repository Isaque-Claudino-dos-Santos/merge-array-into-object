<?php

namespace ISQ\MAIO\Exceptions;

class InvalidTypeToSetException extends \Exception
{
	public function __construct(string $key)
	{
		parent::__construct("Invalid type to set in'{$key}'.");
	}
}
