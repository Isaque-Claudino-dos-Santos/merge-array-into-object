<?php

namespace ISQ\MAIO\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Key
{
    public function __construct(public string $key)
    {
    }
}