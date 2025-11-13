<?php

if (! function_exists('enum_is_backend')) {
	function enum_is_backend(mixed $value): bool
	{
		if (! $value) {
			return false;
		}

		if (! enum_exists($value)) {
			return false;
		}

		return (new ReflectionEnum($value))->isEnum();
	}
}
