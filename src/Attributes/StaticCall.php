<?php

namespace MergeArrayIntoObject\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class StaticCall
{
    public function __construct(public string $methodName)
    {
    }
}
