<?php

namespace ISQ\MAIO\Traits;

trait ResolveSnakeCaseTrait
{
	protected function resolveSnakeCase(string &$key, mixed &$value, ?array $data): void
	{
		$keyInSnakeCase = str_to_snake_case($key);
		$keyExistsInData = array_has_key($data, $keyInSnakeCase);

		if ($keyExistsInData) {
			$key = $keyInSnakeCase;
			$value = array_get($data, $key);
		}
	}
}
