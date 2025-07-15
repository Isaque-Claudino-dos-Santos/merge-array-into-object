<?php

namespace MAIO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Call
{
    public function __construct(
        public string|object $staticClassOrObjectOrFunction,
        public string|null $methodName = null,
    ) {
    }
}
