<?php

if (! function_exists('benchmark')) {
	function benchmark(Closure $callback, int $timers = 1): void
	{
		$start = microtime(true);
		while ($timers--) {
			call_user_func($callback);
		}
		$end = microtime(true);
		dump(round(($end - $start) * 1000, 8)." seconds \n");
	}
}
