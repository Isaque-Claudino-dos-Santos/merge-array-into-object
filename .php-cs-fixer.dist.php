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
	])
	->setIndent("\t")
	->setLineEnding("\n")
;
