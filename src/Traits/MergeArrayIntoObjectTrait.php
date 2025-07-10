<?php

namespace MAIO\Traits;

use MAIO\MergeArrayIntoObject;

trait MergeArrayIntoObjectTrait
{
    public function merge(array $data): self
    {
        $merged = new MergeArrayIntoObject()->merge($this, $data);
        return $merged;
    }
}
