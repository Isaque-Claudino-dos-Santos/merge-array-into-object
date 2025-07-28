<?php

namespace ISQ\MAIO\Exceptions;

use Exception;

class KeyInArrayNotFoundException extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct("Key '{$key}' not found in the provided array.");
    }
}