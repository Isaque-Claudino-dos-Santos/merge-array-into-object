<?php

if (! function_exists('array_has_key')) {
	/**
	 * Check key or path of keys exists in array.
	 *
	 * @param string|int|null $key ex: 0 or 'user.name' or 'age'
	 */
	function array_has_key(array $data, string|int|null $key): bool
	{
		if (null === $key) {
			return false;
		}

		if (is_int($key)) {
			return key_exists($key, $data);
		}

		$paths = explode('.', $key);

		foreach ($paths as $path) {
			if (! key_exists($path, $data)) {
				return false;
			}

			$data = $data[$path];
		}

		return true;
	}
}

if (! function_exists('array_get')) {
	/**
	 * Get value of array by key or path of keys.
	 *
	 * @param string|int|null $key          ex: 0 or 'user.name' or 'age'
	 * @param mixed|null      $defaultValue
	 */
	function array_get(array $data, string|int|null $key, $defaultValue = null): mixed
	{
		if (null === $key) {
			return $defaultValue;
		}

		if (is_int($key)) {
			return key_exists($key, $data) ? $data[$key] : $defaultValue;
		}

		$paths = explode('.', $key);

		foreach ($paths as $path) {
			if (! key_exists($path, $data)) {
				return $defaultValue;
			}

			$data = $data[$path];
		}

		return $data;
	}
}
