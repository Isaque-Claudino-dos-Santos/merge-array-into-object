<?php

use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;


class SnakeCaseMaioTest extends TestCase
{
    #[Test]
    #[Ticket('#19')]
    public function it_should_resolve_snake_case_array_key_in_attribute_of_class_successfully()
    {
        MergeArrayIntoObject::$checkSnakeCase = true;

        $target = new class {
            public string $firstName;
        };

        $data = [
            'first_name' => 'John'
        ];

        $resolved = (new MergeArrayIntoObject)->merge($target, $data);

        $this->assertEquals('John', $resolved->firstName);
    }
}



