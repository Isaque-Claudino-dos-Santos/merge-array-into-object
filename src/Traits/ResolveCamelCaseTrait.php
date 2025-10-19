<?php

namespace ISQ\MAIO\Traits;

trait ResolveCamelCaseTrait
{
	protected function resolveCamelCase(string &$key, mixed &$value, ?array $data): void
	{
		$keyInCamelCase = str_to_camel_case($key);
		$keyExistsInData = array_has_key($data, $keyInCamelCase);

		if ($keyExistsInData) {
			$key = $keyInCamelCase;
			$value = array_get($data, $key);
		}
	}
}
