<?php

namespace ISQ\MAIO\Attributes\Processors;

abstract class Processor
{
	abstract public function process(\ReflectionAttribute $attribute): void;
}
