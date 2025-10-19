<?php

use PhpCsFixer\Config;

return (new Config())
	->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
	->setRules([
		'@PSR12' => true,
		'@PhpCsFixer' => true,
		'@Symfony' => true,
		'@PSR1' => true,
		'@PSR2' => true,
		'single_quote' => ['strings_containing_single_quote_chars' => true],
		'php_unit_method_casing' => false,
		'ternary_to_null_coalescing' => true,
		'standardize_not_equals' => true,
		'not_operator_with_successor_space' => true,
	])
	->setIndent("\t")
	->setLineEnding("\n")
;
