<?php

if (! function_exists('str_to_snake_case')) {
	function str_to_snake_case(string $value, bool $toLower = true): string
	{
		$value = preg_split('/([A-Z][a-z]*)/', $value, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$value = implode('_', $value);

		if ($toLower) {
			$value = strtolower($value);
		}

		return $value;
	}
}

if (! function_exists('str_to_camel_case')) {
	function str_to_camel_case(string $value, ?bool $toTitle = false): string
	{
		$value = preg_split('/_/', $value, flags: PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$value = array_map(fn (string $word) => ucfirst($word), $value);
		$value = implode('', $value);

		if (! $toTitle) {
			$value = lcfirst($value);
		}

		return $value;
	}
}
